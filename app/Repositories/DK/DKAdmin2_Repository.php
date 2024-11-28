<?php
namespace App\Repositories\DK;

use App\Models\DK\DK_User;
use App\Models\DK\DK_UserExt;

use App\Models\DK\DK_District;
use App\Models\DK\DK_Project;
use App\Models\DK\DK_Department;
use App\Models\DK\DK_Pivot_User_Project;
use App\Models\DK\DK_Pivot_Team_Project;
use App\Models\DK\DK_Order;

use App\Models\DK\DK_Client;
use App\Models\DK\DK_Client_Funds_Recharge;
use App\Models\DK\DK_Client_Funds_Using;

use App\Models\DK\DK_Pivot_Client_Delivery;


use App\Models\DK_Choice\DK_Choice_User;
use App\Models\DK_Choice\DK_Choice_Customer;
use App\Models\DK_Choice\DK_Choice_Funds_Recharge;
use App\Models\DK_Choice\DK_Choice_Funds_Using;

use App\Models\DK_Choice\DK_Choice_Project;
use App\Models\DK_Choice\DK_Choice_District;
use App\Models\DK_Choice\DK_Choice_Clue;
use App\Models\DK_Choice\DK_Choice_Record;
use App\Models\DK_Choice\DK_Choice_Record_Visit;
use App\Models\DK_Choice\DK_Choice_Call_Record;

use App\Models\DK_Choice\DK_Choice_Telephone_Bill;



use App\Models\DK_Choice\DK_Choice_Pivot_Customer_Choice;

use App\Models\DK_Customer\DK_Customer_User;
use App\Models\DK_Customer\DK_Customer_Finance_Daily;



use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon, DateTime;
use QrCode, Excel;

class DKAdmin2_Repository {

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
        $this->modelUser = new DK_Choice_User;
        $this->modelItem = new DK_Choice_Clue;

        $this->view_blade_403 = env('TEMPLATE_DK_ADMIN_2').'entrance.errors.403';
        $this->view_blade_404 = env('TEMPLATE_DK_ADMIN_2').'entrance.errors.404';

        Blade::setEchoFormat('%s');
        Blade::setEchoFormat('e(%s)');
        Blade::setEchoFormat('nl2br(e(%s))');
    }


    // 登录情况
    public function get_me()
    {
        if(Auth::guard("dk_admin_2")->check())
        {
            $this->auth_check = 1;
            $this->me = Auth::guard("dk_admin_2")->user();
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
        $query_order = DK_Choice_Clue::select(DB::raw("
                    count(*) as order_count_for_all,
                    count(IF(sale_status = 1, TRUE, NULL)) as order_count_for_put_on,
                    count(IF(sale_result = 1, TRUE, NULL)) as order_count_for_taken,
                    count(IF(sale_result = 9, TRUE, NULL)) as order_count_for_deal
                "));



        // 本月每日工单量
        $query_this_month = DK_Choice_Clue::select('id','created_at')
            ->whereBetween('created_at',[$this_month_start_timestamp,$this_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(created_at,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(created_at,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(created_at,'%e') as day,
                    count(*) as sum
                "));

        // 上月每日工单量
        $query_last_month = DK_Choice_Clue::select('id','created_at')
            ->whereBetween('created_at',[$last_month_start_timestamp,$last_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(created_at,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(created_at,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(created_at,'%e') as day,
                    count(*) as sum
                "));




        $query_order = $query_order->get();

        $return['order_count'] = $query_order[0];


        $statistics_order_this_month_data = $query_this_month->get()->keyBy('day');
        $return['statistics_order_this_month_data'] = $statistics_order_this_month_data;

        $statistics_order_last_month_data = $query_last_month->get()->keyBy('day');
        $return['statistics_order_last_month_data'] = $statistics_order_last_month_data;



        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.index';
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
        $query_order_count_for_unpublished = DK_Order::where('created_type', 1)->where('is_published', 0);
        $query_order_count_for_published = DK_Order::where('created_type', 1)->where('is_published', 1);
        $query_order_count_for_waiting_for_inspect = DK_Order::where('created_type', 1)->where('is_published', 1)->where('inspected_status', 0);
        $query_order_count_for_inspected = DK_Order::where('created_type', 1)->where('is_published', 1)->where('inspected_status', '<>', 0);
        $query_order_count_for_accepted = DK_Order::where('created_type', 1)->where('is_published', 1)->where('inspected_result','通过');
        $query_order_count_for_refused = DK_Order::where('created_type', 1)->where('is_published', 1)->where('inspected_result','拒绝');
        $query_order_count_for_accepted_inside = DK_Order::where('created_type', 1)->where('is_published', 1)->where('inspected_result','内部通过');
        $query_order_count_for_repeat = DK_Order::where('created_type', 1)->where('is_published', 1)->where('is_repeat','>',0);



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
//            $subordinates_array = DK_Choice_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_Choice_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();

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
//            $subordinates_array = DK_Choice_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_Choice_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();

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
//            $subordinates_array = DK_Choice_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
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
//            $subordinates = DK_Choice_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $query->where('is_published','<>',0)->whereHas('project_er', function ($query) use ($subordinates) {
//                $query->whereIn('user_id', $subordinates);
//            });

            $subordinates_array = DK_Choice_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
            $project_array = DK_Choice_Project::select('id')->whereIn('user_id',$subordinates_array)->get()->pluck('id')->toArray();

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

            $project_array = DK_Choice_Project::select('id')->where('user_id',$me->id)->get()->pluck('id')->toArray();

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



        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.index';
        return view($view_blade)->with($return);
    }


    // 返回（后台）主页视图
    public function view_admin_404()
    {
        $this->get_me();
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.errors.404';
        return view($view_blade);
    }




    /*
     * select2
     */
    //
    public function operate_select2_project($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(empty($post_data['keyword']))
        {
            $query = DK_Choice_Project::select(['id','name as text']);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query = DK_Choice_Project::select(['id','name as text'])->where('name','like',"%$keyword%");
        }

        $query->where('item_status',1);
//        $query->where(['user_status'=>1,'user_category'=>11]);
//        $query->whereIn('user_type',[41,61,88]);


        $list = $query->get()->toArray();
        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
        array_unshift($list,$unSpecified);
        return $list;
    }
    //
    public function operate_select2_customer($post_data)
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
//        if(!is_numeric($type)) return view(env('TEMPLATE_DK_ADMIN_2').'errors.404');
//        if(!in_array($type,[1,2,3,10,11,88])) return view(env('TEMPLATE_DK_ADMIN_2').'errors.404');

        if(empty($post_data['keyword']))
        {
            $list =DK_Choice_Customer::select(['id','username as text'])
                ->where(['user_status'=>1,'user_category'=>11])
//                ->whereIn('user_type',[41,61,88])
                ->get()->toArray();
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $list =DK_Choice_Customer::select(['id','username as text'])->where('username','like',"%$keyword%")
                ->where(['user_status'=>1,'user_category'=>11])
//                ->whereIn('user_type',[41,61,88])
                ->get()->toArray();
        }
        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
        array_unshift($list,$unSpecified);
        return $list;
    }
    // 【地域】select2
    public function operate_district_select2_district($post_data)
    {
        if(empty($post_data['keyword']))
        {
            $query =DK_Choice_District::select(['id','district_district as text'])
                ->where(['district_status'=>1]);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query =DK_Choice_District::select(['id','district_district as text'])->where('district_district','like',"%$keyword%")
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




    /*
     * 用户基本信息
     */
    // 【基本信息】返回视图
    public function view_my_profile_info_index()
    {
        $this->get_me();
        $me = $this->me;

        $return['data'] = $me;

        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.my-account.my-profile-info-index';
        return view($view_blade)->with($return);
    }
    // 【基本信息】返回-编辑-视图
    public function view_my_profile_info_edit()
    {
        $this->get_me();
        $me = $this->me;

        $return['data'] = $me;

        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.my-account.my-profile-info-edit';
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

        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.my-account.my-account-password-change';
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
    public function view_user_customer_list($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61])) return view($this->view_blade_403);

        $return['menu_active_of_customer_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.user.customer-list';
        return view($view_blade)->with($return);
    }
    // 【客户】返回-列表-数据
    public function get_user_customer_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Choice_Customer::select('*')
            ->with(['creator','customer_admin_er'])
            ->whereIn('user_category',[11])
            ->whereIn('user_type',[0,1,9,11,19,21,22,41,61]);

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


    // 【客户】【修改记录】返回-列表-视图
    public function view_user_customer_modify_record($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $staff_list = DK_Choice_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();

        $return['staff_list'] = $staff_list;
        $return['menu_active_of_customer_modify_list'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.user.customer-modify-list';
        return view($view_blade)->with($return);
    }
    // 【客户】【修改记录】返回-列表-数据
    public function get_user_customer_modify_record_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Choice_Record::select('*')
            ->with(['creator'])
            ->where(['record_object'=>21, 'operate_object'=>21,'item_id'=>$id]);

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


    // 【客户】返回-添加-视图
    public function view_user_customer_create()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61])) return view($this->view_blade_403);

        $item_type = 'item';
        $item_type_text = '客户';
        $title_text = '添加'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/user/customer-list';

        $view_data['operate'] = 'create';
        $view_data['operate_id'] = 0;
        $view_data['category'] = 'customer';
        $view_data['type'] = $item_type;
        $view_data['item_type_text'] = $item_type_text;
        $view_data['title_text'] = $title_text;
        $view_data['list_text'] = $list_text;
        $view_data['list_link'] = $list_link;

        $district_city_list = DK_Choice_District::select('id','district_city')->whereIn('district_status',[1])->get();
        $view_data['district_city_list'] = $district_city_list;

        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.user.customer-edit';
        return view($view_blade)->with($view_data);
    }
    // 【客户】返回-编辑-视图
    public function view_user_customer_edit()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61])) return view($this->view_blade_403);

        $id = request("id",0);
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.user.customer-edit';

        $item_type = 'item';
        $item_type_text = '客户';
        $title_text = '编辑'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/user/customer-list';

        $view_data['operate'] = 'create';
        $view_data['operate_id'] = 0;
        $view_data['category'] = 'customer';
        $view_data['type'] = $item_type;
        $view_data['item_type_text'] = $item_type_text;
        $view_data['title_text'] = $title_text;
        $view_data['list_text'] = $list_text;
        $view_data['list_link'] = $list_link;

        $district_city_list = DK_Choice_District::select('id','district_city')->whereIn('district_status',[1])->get();
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

        if($id == 0)
        {
            return view($view_blade)->with($view_data);
        }
        else
        {
            $view_data['operate'] = 'edit';
            $view_data['operate_id'] = $id;

            $mine = DK_Choice_Customer::with(['customer_admin_er'])->find($id);
            if($mine)
            {
                if($mine->customer_admin_er)
                {
                    $mine->customer_admin_name = $mine->customer_admin_er->username;
                    $mine->customer_admin_mobile = $mine->customer_admin_er->mobile;
                    $mine->customer_admin_api_agent_id = $mine->customer_admin_er->api_agent_id;
                }
                $view_data['data'] = $mine;
                return view($view_blade)->with($view_data);
            }
            else return view(env('TEMPLATE_DK_ADMIN_2').'errors.404');
        }
    }
    // 【客户】保存数据
    public function operate_user_customer_save($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => 'operate.required.',
            'username.required' => '请输入客户名称！',
            'cooperative_unit_price_1.required' => '请输入合作单价！',
            'cooperative_unit_price_2.required' => '请输入合作单价！',
            'cooperative_unit_price_3.required' => '请输入合作单价！',
            'cooperative_unit_price_of_telephone.required' => '请输入合作单价！',
//            'username.unique' => '该客户已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'username' => 'required',
            'cooperative_unit_price_1' => 'required',
            'cooperative_unit_price_2' => 'required',
            'cooperative_unit_price_3' => 'required',
            'cooperative_unit_price_of_telephone' => 'required',
//            'username' => 'required|unique:dk_customer,username',
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
            $is_username_exist = DK_Choice_Customer::select('id')->where('username',$post_data["username"])->count();
            if($is_username_exist) return response_error([],"该客户名已存在，请勿重复添加！");

            $is_mobile_exist = DK_Choice_Customer::select('id')->where('mobile',$post_data["customer_admin_mobile"])->count();
            if($is_mobile_exist) return response_error([],"该电话已存在，请勿重复添加！");

            $is_mobile_exist = DK_Customer_User::select('id')->where('mobile',$post_data["customer_admin_mobile"])->count();
            if($is_mobile_exist) return response_error([],"该电话已存在，请勿重复添加！");

            $customer = new DK_Choice_Customer;
            $customer_data["user_category"] = 11;
            $customer_staff_data["user_type"] = 11;
            $customer_data["active"] = 1;
            $customer_data["creator_id"] = $me->id;
            $customer_data["username"] = $post_data["username"];
            $customer_data["mobile"] = $post_data["customer_admin_mobile"];
            $customer_data["customer_admin_name"] = $post_data["customer_admin_name"];
            $customer_data["customer_admin_mobile"] = $post_data["customer_admin_mobile"];
            $customer_data["cooperative_unit_price_1"] = $post_data["cooperative_unit_price_1"];
            $customer_data["cooperative_unit_price_2"] = $post_data["cooperative_unit_price_2"];
            $customer_data["cooperative_unit_price_3"] = $post_data["cooperative_unit_price_3"];
            $customer_data["cooperative_unit_price_of_telephone"] = $post_data["cooperative_unit_price_of_telephone"];
            $customer_data["call_time_limit_for_clue"] = $post_data["call_time_limit_for_clue"];
            $customer_data["call_time_limit_for_telephone"] = $post_data["call_time_limit_for_telephone"];
            $customer_data["api_id"] = $post_data["api_id"];
            $customer_data["api_password"] = $post_data["api_password"];
            $customer_data["is_ip"] = $post_data["is_ip"];
            $customer_data["ip_whitelist"] = $post_data["ip_whitelist"];
            $customer_data["password"] = password_encode("12345678");
            $customer_data["district_city"] = $post_data["district_city"];
            if(!empty($post_data["district_district"]))
            {
                $customer_data["district_district"] = implode('-', $post_data["district_district"]);
            }
            else $customer_data["district_district"] = '';

            $customer_staff = new DK_Customer_User;
            $customer_staff_data["user_category"] = 11;
            $customer_staff_data["user_type"] = 11;
            $customer_staff_data["active"] = 1;
            $customer_staff_data["username"] = $post_data["customer_admin_name"];
            $customer_staff_data["mobile"] = $post_data["customer_admin_mobile"];
            $customer_staff_data["api_agent_id"] = $post_data["customer_admin_api_agent_id"];
            $customer_staff_data["creator_id"] = 0;
            $customer_staff_data["password"] = password_encode("12345678");
        }
        else if($operate == 'edit') // 编辑
        {
            // 该客户是否存在
            $customer = DK_Choice_Customer::find($operate_id);
            if(!$customer) return response_error([],"该客户不存在，刷新页面重试！");

            $customer_data["username"] = $post_data["username"];
            $customer_data["mobile"] = $post_data["customer_admin_mobile"];
//            $customer_data["customer_admin_name"] = $post_data["customer_admin_name"];
//            $customer_data["customer_admin_mobile"] = $post_data["customer_admin_mobile"];
//            $customer_data["customer_admin_api_agent_id"] = $post_data["customer_admin_api_agent_id"];
            $customer_data["cooperative_unit_price_1"] = $post_data["cooperative_unit_price_1"];
            $customer_data["cooperative_unit_price_2"] = $post_data["cooperative_unit_price_2"];
            $customer_data["cooperative_unit_price_3"] = $post_data["cooperative_unit_price_3"];
            $customer_data["cooperative_unit_price_of_telephone"] = $post_data["cooperative_unit_price_of_telephone"];
            $customer_data["call_time_limit_for_clue"] = $post_data["call_time_limit_for_clue"];
            $customer_data["call_time_limit_for_telephone"] = $post_data["call_time_limit_for_telephone"];
            $customer_data["api_id"] = $post_data["api_id"];
            $customer_data["api_password"] = $post_data["api_password"];
            $customer_data["is_ip"] = $post_data["is_ip"];
            $customer_data["ip_whitelist"] = $post_data["ip_whitelist"];
            $customer_data["district_city"] = $post_data["district_city"];
            if(!empty($post_data["district_district"]))
            {
                $customer_data["district_district"] = implode('-', $post_data["district_district"]);
            }
            else $customer_data["district_district"] = '';

            // 名称是否存在
            $is_username_exist = DK_Choice_Customer::select('id')->where('id','<>',$operate_id)->where('username',$post_data["username"])->count();
            if($is_username_exist) return response_error([],"该客户名已存在，不能修改成此客户名！");

            // 客户管理员是否存在
            $customer_staff = DK_Customer_User::where('id', $customer->customer_admin_id)->first();
            if($customer_staff)
            {
                // 客户管理员存在

                // 判断电话是否重复
                $is_mobile_exist = DK_Customer_User::select('id')->where('id','<>',$customer->customer_admin_id)->where('mobile',$post_data["customer_admin_mobile"])->count();
                if($is_mobile_exist) return response_error([],"该电话已存在，不能修改成此电话！");

                $customer_staff_data["username"] = $post_data["customer_admin_name"];
                $customer_staff_data["mobile"] = $post_data["customer_admin_mobile"];
                $customer_staff_data["api_agent_id"] = $post_data["customer_admin_api_agent_id"];
            }
            else
            {
                // 客户管理员不存在

                // 判断电话是否重复
                $is_mobile_exist = DK_Customer_User::select('id')->where('mobile',$post_data["customer_admin_mobile"])->count();
                if($is_mobile_exist) return response_error([],"该电话已存在，不能修改成此电话啊！");

                $customer_staff = new DK_Customer_User;
                $customer_staff_data["user_category"] = 11;
                $customer_staff_data["user_type"] = 11;
                $customer_staff_data["active"] = 1;
                $customer_staff_data["customer_id"] = $customer->id;
                $customer_staff_data["username"] = $post_data["customer_admin_name"];
                $customer_staff_data["mobile"] = $post_data["customer_admin_mobile"];
                $customer_staff_data["api_agent_id"] = $post_data["customer_admin_api_agent_id"];
                $customer_staff_data["creator_id"] = 0;
                $customer_staff_data["password"] = password_encode("12345678");
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


            $bool = $customer->fill($customer_data)->save();
            if($bool)
            {
                if($operate == 'create')
                {
                    $customer_staff_data["customer_id"] = $customer->id;
                }

                $bool_1 = $customer_staff->fill($customer_staff_data)->save();
                if($bool_1)
                {
                    if($operate == 'create')
                    {
                        $customer->customer_admin_id = $customer_staff->id;
                        $bool = $customer->save();
                        if(!$bool) throw new Exception("update--customer--fail");
                    }
                }
                else throw new Exception("insert--customer-staff--fail");
            }
            else throw new Exception("insert--customer--fail");

            DB::commit();
            return response_success(['id'=>$customer->id]);
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
    public function operate_customer_info_text_set($post_data)
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
        if($operate != 'user-customer-info-text-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Choice_Customer::withTrashed()->find($id);
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
                    $record = new DK_Choice_Record;

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
    public function operate_customer_info_time_set($post_data)
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
        if($operate != 'user-customer-info-time-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Choice_Customer::withTrashed()->find($id);
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
                    $record = new DK_Choice_Record;

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
    public function operate_customer_info_option_set($post_data)
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
        if($operate != 'user-customer-info-option-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Choice_Customer::withTrashed()->find($id);
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
                    $record = new DK_Choice_Record;

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
    public function operate_customer_info_attachment_set($post_data)
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
        if($operate != 'user-customer-attachment-set') return response_error([],"参数[operate]有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数[ID]有误！");

        $item = DK_Choice_Customer::withTrashed()->find($item_id);
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
                                $record = new DK_Choice_Record;

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
                        $record = new DK_Choice_Record;

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
    public function operate_customer_info_attachment_delete($post_data)
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
        if($operate != 'user-customer-attachment-delete') return response_error([],"参数【operate】有误！");
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
                $record = new DK_Choice_Record;

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
    public function operate_customer_get_attachment_html($post_data)
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

        $item = DK_Choice_Customer::with([
            'attachment_list' => function($query) { $query->where(['record_object'=>21, 'operate_object'=>41]); }
        ])->withTrashed()->find($id);
        if(!$item) return response_error([],"该【部门】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");


        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.item-assign-html-for-attachment';
        $html = view($view_blade)->with(['item_list'=>$item->attachment_list])->__toString();

        return response_success(['html'=>$html],"");
    }


    // 【客户】管理员-修改密码
    public function operate_user_customer_password_admin_change($post_data)
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
        if($operate != 'customer-password-admin-change') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $user = DK_Choice_Customer::withTrashed()->find($id);
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
    public function operate_user_customer_password_admin_reset($post_data)
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
        if($operate != 'customer-password-admin-reset') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $customer = DK_Choice_Customer::withTrashed()->find($id);
        if(!$customer) return response_error([],"该客户不存在，刷新页面重试！");

        $customer_staff = DK_Customer_User::withTrashed()->where('customer_id',$customer->id)->first();
        if(!$customer_staff) return response_error([],"该客户不存在，刷新页面重试！");

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
            $customer_staff->password = password_encode('12345678');
            $bool = $customer_staff->save();
            if(!$bool) throw new Exception("update--customer-staff--fail");

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
    public function operate_user_customer_admin_enable($post_data)
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
        if($operate != 'customer-admin-enable') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $user = DK_Choice_Customer::find($id);
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
            if(!$bool) throw new Exception("update--customer--fail");

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
    public function operate_user_customer_admin_disable($post_data)
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
        if($operate != 'customer-admin-disable') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $user = DK_Choice_Customer::find($id);
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
            if(!$bool) throw new Exception("update--customer--fail");

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
    public function view_user_customer_finance_recharge_record($post_data)
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
    public function get_user_customer_recharge_record_datatable($post_data)
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


    // 【客户】添加-财务数据-保存数据（充值）
    public function operate_user_customer_finance_recharge_create($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => 'operate.required.',
            'customer_id.required' => 'customer_id.required.',
            'transaction_date.required' => '请选择交易日期！',
            'transaction_title.required' => '请填写费用类型！',
            'transaction_type.required' => '请填写支付方式！',
            'transaction_amount.required' => '请填写金额！',
//            'transaction_account.required' => '请填写交易账号！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'customer_id' => 'required',
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

        $customer_id = $post_data["customer_id"];
        $customer = DK_Choice_Customer::find($customer_id);
        if(!$customer) return response_error([],"该【客户】不存在，刷新页面重试！");

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
            $FinanceRecord = new DK_Choice_Funds_Recharge;

//            if(in_array($me->user_type,[11,19,41,42]))
//            {
//                $FinanceRecord_data['is_confirmed'] = 1;
//            }

            $FinanceRecord_data['creator_id'] = $me->id;
            $FinanceRecord_data['finance_category'] = 11;
            $FinanceRecord_data['finance_type'] = $finance_type;
            $FinanceRecord_data['customer_id'] = $post_data["customer_id"];
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
                $customer = DK_Choice_Customer::lockForUpdate()->find($customer_id);

//                if(in_array($me->user_type,[11,19,41,42]))
                if(in_array($me->user_type,[-1]))
                {
                    if($finance_type == 1)
                    {
                        $customer->funds_recharge_total = $customer->funds_recharge_total + $transaction_amount;
//                        $customer->funds_balance = $customer->funds_balance + $transaction_amount;
                    }
                    else if($finance_type == 101)
                    {
                        $customer->funds_recharge_total = $customer->funds_recharge_total - $transaction_amount;
//                        $customer->funds_balance = $company->funds_balance - $transaction_amount;
                    }
                }
                else
                {
                    if($finance_type == 1)
                    {
                        $customer->funds_recharge_total = $customer->funds_recharge_total + $transaction_amount;
//                        $customer->funds_balance = $customer->funds_balance + $transaction_amount;
                    }
                    else if($finance_type == 101)
                    {
                        $customer->funds_recharge_total = $customer->funds_recharge_total - $transaction_amount;
//                        $customer->funds_balance = $customer->funds_balance - $transaction_amount;
                    }
                }

                $bool_1 = $customer->save();
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
     * 部门管理
     */
    // select2
    public function operate_department_select2_leader($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(empty($post_data['keyword']))
        {
            $query =DK_Choice_User::select(['id','username as text'])
                ->where(['user_status'=>1]);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query =DK_Choice_User::select(['id','username as text'])->where('username','like',"%$keyword%")
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
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.department.department-list-for-all';
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
                $query->where('item_type', $post_data['department_type']);
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
                $v->district_id = $v->superior_department_id;
            }

            $v->district_group_id = $v->district_id.'.'.$v->id;
        }
//        $list = $list->sortBy(['district_id'=>'asc'])->values();
        $list = $list->sortBy(function ($item, $key) {
            return $item['district_group_id'];
        })->values();
//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }


    // 【部门】【修改记录】返回-列表-视图
    public function view_department_modify_record($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $staff_list = DK_Choice_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();

        $return['staff_list'] = $staff_list;
        $return['menu_active_of_car_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.department-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【部门】【修改记录】返回-列表-数据
    public function get_department_modify_record_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Choice_Record::select('*')
            ->with(['creator'])
            ->where(['record_object'=>21, 'operate_object'=>31,'item_id'=>$id]);

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

        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.department.department-edit';
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
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.department.department-edit';

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
//                if(!in_array($mine->user_category,[1,9,11,88])) return view(env('TEMPLATE_DK_ADMIN_2').'errors.404');
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
            else return view(env('TEMPLATE_DK_ADMIN_2').'errors.404');
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
                    $record = new DK_Choice_Record;

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
                    $record = new DK_Choice_Record;

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
                    $record = new DK_Choice_Record;

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
                                $record = new DK_Choice_Record;

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
                        $record = new DK_Choice_Record;

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
                $record = new DK_Choice_Record;

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


        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.item-assign-html-for-attachment';
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

        $item = DK_Choice_Project::withTrashed()->find($id);
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

        $item = DK_Choice_Project::withTrashed()->find($id);
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
    public function operate_user_select2_choice($post_data)
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
//        if(!is_numeric($type)) return view(env('TEMPLATE_DK_ADMIN_2').'errors.404');
//        if(!in_array($type,[1,2,3,10,11,88])) return view(env('TEMPLATE_DK_ADMIN_2').'errors.404');

        if(empty($post_data['keyword']))
        {
            $list =DK_Choice_User::select(['id','username as text'])
                ->where(['user_category'=>11])->whereIn('user_type',[41,61,88])
                ->get()->toArray();
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $list =DK_Choice_User::select(['id','username as text'])->where('username','like',"%$keyword%")
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
            $query =DK_Choice_User::select(['id','true_name as text'])
                ->where(['user_status'=>1]);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query =DK_Choice_User::select(['id','true_name as text'])->where('username','like',"%$keyword%")
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
    public function view_user_staff_list($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,21,31,41,61,71,81])) return view($this->view_blade_403);

        if(in_array($me->user_type,[0,1,9,11]))
        {
            $department_district_list = DK_Department::select('id','name')->where('department_type',11)->get();
            $return['department_district_list'] = $department_district_list;
        }

        $return['menu_active_of_staff_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.user.staff-list';
        return view($view_blade)->with($return);
    }
    // 【员工】返回-列表-数据
    public function get_user_staff_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Choice_User::withTrashed()->select('*')
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

        $staff_list = DK_Choice_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();

        $return['staff_list'] = $staff_list;
        $return['menu_active_of_staff_modify_list'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.user.staff-modify-list';
        return view($view_blade)->with($return);
    }
    // 【客户管理】【修改记录】返回-列表-数据
    public function get_user_staff_modify_record_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Choice_Record::select('*')
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

        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.user.staff-edit';
        return view($view_blade)->with($return_data);
    }
    // 【员工】返回-编辑-视图
    public function view_user_staff_edit()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,19,21,31,41,61,71,81])) return view($this->view_blade_403);

        $id = request("id",0);
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.user.staff-edit';

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
            $mine = DK_Choice_User::with(['parent','superior'])->find($id);
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
            else return view(env('TEMPLATE_DK_ADMIN_2').'entrance.errors.404');
        }
    }
    // 【员工】保存数据
    public function operate_user_staff_save($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => '参数有误！',
            'username.required' => '请输入用户名！',
            'mobile.required' => '请输入电话！',
//            'mobile.unique' => '电话已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'username' => 'required',
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
            $is_exist = DK_Choice_User::where('mobile',$post_data['mobile'])->first();
            if($is_exist) return response_error([],"工号已存在！");

            $mine = new DK_Choice_User;
            $post_data["user_status"] = 0;
            $post_data["user_category"] = 11;
            $post_data["active"] = 1;
            $post_data["password"] = password_encode("12345678");
            $post_data["creator_id"] = $me->id;
            $post_data['username'] = $post_data['true_name'];
        }
        else if($operate == 'edit') // 编辑
        {
            $mine = DK_Choice_User::find($operate_id);
            if(!$mine) return response_error([],"该用户不存在，刷新页面重试！");
            if($mine->mobile != $post_data['mobile'])
            {
                $is_exist = DK_Choice_User::where('mobile',$post_data['mobile'])->first();
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
//                    $user_ext = new DK_UserExt;
//                    $user_ext_create['user_id'] = $mine->id;
//                    $bool_2 = $user_ext->fill($user_ext_create)->save();
//                    if(!$bool_2) throw new Exception("insert--user-ext--failed");
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

        $item = DK_Choice_User::withTrashed()->find($id);
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
                    $record = new DK_Choice_Record;

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

        $item = DK_Choice_User::withTrashed()->find($id);
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
                    $record = new DK_Choice_Record;

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

        $item = DK_Choice_User::withTrashed()->find($id);
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
                    $record = new DK_Choice_Record;

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

        $item = DK_Choice_User::withTrashed()->find($item_id);
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
                                $record = new DK_Choice_Record;

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
                        $record = new DK_Choice_Record;

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
        if($operate != 'user-customer-attachment-delete') return response_error([],"参数【operate】有误！");
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
                $record = new DK_Choice_Record;

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

        $item = DK_Choice_User::with([
            'attachment_list' => function($query) { $query->where(['record_object'=>21, 'operate_object'=>41]); }
        ])->withTrashed()->find($id);
        if(!$item) return response_error([],"该【部门】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");


        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.item-assign-html-for-attachment';
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

        $user = DK_Choice_User::withTrashed()->find($id);
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

        $user = DK_Choice_User::withTrashed()->find($id);
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

        $user = DK_Choice_User::withTrashed()->find($id);
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

        $user = DK_Choice_User::withTrashed()->find($id);
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

        $user = DK_Choice_User::withTrashed()->find($id);
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

        $user = DK_Choice_User::find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($me->user_category != 0) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->login_error_num = 0;
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

        $user = DK_Choice_User::find($id);
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

        $user = DK_Choice_User::find($id);
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

        $user = DK_Choice_User::find($id);
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

        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.item-edit';
        return view($view_blade)->with($return);
    }
    // 【内容】返回-编辑-视图
    public function view_item_item_edit($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,21,22])) return view(env('TEMPLATE_DK_ADMIN_2').'errors.404');

        $id = $post_data["item-id"];
        $mine = $this->modelItem->with(['owner'])->find($id);
        if(!$mine) return view(env('TEMPLATE_DK_ADMIN_2').'errors.404');


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

        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.item-edit';
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



    // 【地域】返回-列表-视图
    public function view_item_district_list($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61])) return view($this->view_blade_403);

        $return['menu_active_of_district_list'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.district-list';
        return view($view_blade)->with($return);
    }
    // 【地域】返回-列表-数据
    public function get_item_district_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $query = DK_Choice_District::select('*')
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

        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.district-edit';
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
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.district-edit';

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
            $mine = DK_Choice_District::find($id);
            if($mine)
            {
//                if(!in_array($mine->user_category,[1,9,11,88])) return view(env('TEMPLATE_DK_ADMIN_2').'errors.404');
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
            else return view(env('TEMPLATE_DK_ADMIN_2').'errors.404');
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
            $is_exist = DK_Choice_District::select('id')->where('district_city',$post_data["district_city"])->count();
            if($is_exist) return response_error([],"该【城市】已存在，请勿重复添加！");

            $mine = new DK_Choice_District;
            $post_data["active"] = 1;
            $post_data["district_status"] = 1;
            $post_data["creator_id"] = $me->id;
        }
        else if($operate == 'edit') // 编辑
        {
            $mine = DK_Choice_District::find($operate_id);
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

        $item = DK_Choice_District::withTrashed()->find($item_id);
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

        $item = DK_Choice_District::withTrashed()->find($id);
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

        $item = DK_Choice_District::withTrashed()->find($id);
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

        $item = DK_Choice_District::find($id);
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

        $item = DK_Choice_District::find($id);
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
            $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.project-list-for-department';
        }
        else $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.project-list';
        return view($view_blade)->with($return);
    }
    // 【项目】返回-列表-数据
    public function get_item_project_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $query = DK_Choice_Project::select('*')
            ->withTrashed()
            ->with(['creator','customer_er','inspector_er']);

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
            $inspector_list = DK_Choice_User::select('id')->whereIn('user_type',[71,77])->where('department_district_id',$department_district_id)->get();
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

        $staff_list = DK_Choice_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();

        $return['staff_list'] = $staff_list;
        $return['menu_active_of_car_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.project-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【项目】【修改记录】返回-列表-数据
    public function get_item_project_modify_record_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Choice_Record::select('*')
            ->with(['creator'])
            ->where(['record_object'=>21,'operate_object'=>61,'item_id'=>$id]);

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

        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.project-edit';
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
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.project-edit';

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
            $mine = DK_Choice_Project::with('inspector_er')->find($id);
            if($mine)
            {
//                if(!in_array($mine->user_category,[1,9,11,88])) return view(env('TEMPLATE_DK_ADMIN_2').'errors.404');
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
            else return view(env('TEMPLATE_DK_ADMIN_2').'errors.404');
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
            $is_exist = DK_Choice_Project::select('id')->where('name',$post_data["name"])->count();
            if($is_exist) return response_error([],"该【项目】已存在，请勿重复添加！");

            $mine = new DK_Choice_Project;
            $post_data["active"] = 1;
            $post_data["creator_id"] = $me->id;
        }
        else if($operate == 'edit') // 编辑
        {
            $mine = DK_Choice_Project::find($operate_id);
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

        $item = DK_Choice_Project::withTrashed()->find($id);
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
                    $record = new DK_Choice_Record;

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

        $item = DK_Choice_Project::withTrashed()->find($id);
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
                    $record = new DK_Choice_Record;

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

        $item = DK_Choice_Project::withTrashed()->find($id);
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
                    $record = new DK_Choice_Record;

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

                    if(in_array($column_key,['customer_id']))
                    {
                        $record_data["before_id"] = $before;
                        $record_data["after_id"] = $column_value;
                    }

                    if($column_key == 'customer_id')
                    {
                        $record_data["before_customer_id"] = $before;
                        $record_data["after_customer_id"] = $column_value;
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

        $item = DK_Choice_Project::withTrashed()->find($item_id);
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
                                $record = new DK_Choice_Record;

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
                        $record = new DK_Choice_Record;

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
                $record = new DK_Choice_Record;

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

        $item = DK_Choice_Project::with([
                'attachment_list' => function($query) { $query->where(['record_object'=>21, 'operate_object'=>61]); }
            ])->withTrashed()->find($id);
        if(!$item) return response_error([],"该【项目】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");


        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.item-assign-html-for-attachment';
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

        $item = DK_Choice_Project::withTrashed()->find($item_id);
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

        $item = DK_Choice_Project::withTrashed()->find($id);
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

        $item = DK_Choice_Project::withTrashed()->find($id);
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

        $item = DK_Choice_Project::find($id);
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

        $item = DK_Choice_Project::find($id);
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









    // 【线索】返回-列表-视图
    public function view_item_telephone_list($post_data)
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




        // 工单ID
        if(!empty($post_data['clue_id']))
        {
            if(is_numeric($post_data['clue_id']) && $post_data['clue_id'] > 0) $view_data['clue_id'] = $post_data['clue_id'];
            else $view_data['clue_id'] = '';
        }
        else $view_data['clue_id'] = '';

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




        // 客户
        if(!empty($post_data['customer_id']))
        {
            if(is_numeric($post_data['customer_id']) && $post_data['customer_id'] > 0) $view_data['customer_id'] = $post_data['customer_id'];
            else $view_data['customer_id'] = -1;
        }
        else $view_data['customer_id'] = -1;




        // 项目
        if(!empty($post_data['project_id']))
        {
            if(is_numeric($post_data['project_id']) && $post_data['project_id'] > 0)
            {
                $project = DK_Choice_Project::select(['id','name'])->find($post_data['project_id']);
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
        if(!empty($post_data['customer_name']))
        {
            if($post_data['customer_name']) $view_data['customer_name'] = $post_data['customer_name'];
            else $view_data['customer_name'] = '';
        }
        else $view_data['customer_'] = '';
        // 客户电话
        if(!empty($post_data['customer_phone']))
        {
            if($post_data['customer_phone']) $view_data['customer_phone'] = $post_data['customer_phone'];
            else $view_data['customer_phone'] = '';
        }
        else $view_data['customer_phone'] = '';

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


        $customer_list = DK_Choice_Customer::select('id','username')->where('user_category',11)->get();
        $view_data['customer_list'] = $customer_list;

        $customer_preferential_list = DK_Choice_Customer::select('id','username')->where('user_category',11)->where('is_preferential',1)->get();
        $view_data['customer_preferential_list'] = $customer_preferential_list;

        $department_district_list = DK_Department::select('id','name')->where('department_type',11)->get();
        $view_data['department_district_list'] = $department_district_list;


        $project_list = DK_Choice_Project::select('id','name')->whereIn('item_type',[1,21])->get();
        $view_data['project_list'] = $project_list;

        $district_city_list = DK_Choice_District::select('id','district_city')->whereIn('district_status',[1])->get();
        $view_data['district_city_list'] = $district_city_list;

        if(!empty($post_data['district_city']))
        {
            $district_district_list = DK_Choice_District::select('district_district')->where('district_city',$post_data['district_city'])->whereIn('district_status',[1])->get();
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

        $view_data['menu_active_of_telephone_list'] = 'active menu-open';

        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.telephone-list';
        return view($view_blade)->with($view_data);
    }
    // 【线索】返回-列表-数据
    public function get_item_telephone_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Choice_Telephone_Bill::select('*')
//            ->selectAdd(DB::Raw("FROM_UNIXTIME(assign_time, '%Y-%m-%d') as assign_date"))
            ->with([
                'creator',
                'owner'=>function($query) { $query->select('id','username'); },
                'customer_er'=>function($query) { $query->select('id','username'); }
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



        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");
        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        if(!empty($post_data['customer_name'])) $query->where('customer_name', $post_data['customer_name']);
        if(!empty($post_data['customer_phone'])) $query->where('customer_phone', 'like', "%{$post_data['customer_phone']}");

        if(!empty($post_data['assign'])) $query->whereDate(DB::Raw("from_unixtime(created_at)"), $post_data['assign']);
        if(!empty($post_data['assign_start'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '>=', $post_data['assign_start']);
        if(!empty($post_data['assign_ended'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '<=', $post_data['assign_ended']);



        // 客户
        if(isset($post_data['customer']))
        {
            if(!in_array($post_data['customer'],[-1]))
            {
                $query->where('customer_id', $post_data['customer']);
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
                    $customer_phone = $v->customer_phone;
                    $v->customer_phone = substr($customer_phone, 0, 3).'****'.substr($customer_phone, -4);
                }
            }
            else if(in_array($me->user_type,[41,81,84,88]))
            {
                $time = time();
                if(!$v->is_me || (($v->published_at > 0) && (($time - $v->published_at) > 86400)))
                {
//                    $len = strlen($customer_phone);  // 字符串长度
                    $customer_phone = $v->customer_phone;
                    if(is_numeric($customer_phone))
                    {
                        $v->customer_phone = substr($customer_phone, 0, 3).'****'.substr($customer_phone, -4);
                    }
                }
            }

        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    // 【线索】【修改记录】返回-列表-视图
    public function view_item_telephone_modify_record($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $return['menu_active_of_clue_modify_list'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.clue-modify-list';
        return view($view_blade)->with($return);
    }
    // 【线索】【修改记录】返回-列表-数据
    public function get_item_telephone_modify_record_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Choice_Record::select('*')
            ->with([
                'creator'=>function($query) { $query->select('id','username'); },
                'choice_staff_er'=>function($query) { $query->select('id','username'); },
                'customer_staff_er'=>function($query) { $query->select('id','username'); }
            ])
            ->where(['clue_id'=>$id]);
//            ->where(['record_object'=>21,'operate_object'=>61,'item_id'=>$id]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        if(!in_array($me->user_type,[0,1,9,11,61,66]))
        {
            $query->whereNotIn('operate_category',[96]);
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


    // 【线索】返回-导入-视图
    public function view_item_telephone_import()
    {
        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9])) return view(env('TEMPLATE_ROOT_FRONT').'errors.404');

        $operate_category = 'item';
        $operate_type = 'item';
        $operate_type_text = '话单';
        $title_text = '导入'.$operate_type_text;
        $list_text = $operate_type_text.'列表';
        $list_link = '/item/clue-list';

        $return['operate'] = 'create';
        $return['operate_id'] = 0;
        $return['operate_category'] = $operate_category;
        $return['operate_type'] = $operate_type;
        $return['operate_type_text'] = $operate_type_text;
        $return['title_text'] = $title_text;
        $return['list_text'] = $list_text;
        $return['list_link'] = $list_link;

        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.telephone-edit-for-import';
        return view($view_blade)->with($return);
    }
    // 【线索】保存-导入-数据
    public function operate_item_telephone_import_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required',
            'customer_id.required' => '请填选择客户！',
            'customer_id.numeric' => '选择客户参数有误！',
            'customer_id.min' => '请填客户ID有误！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'customer_id' => 'required|numeric|min:1',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        $customer_id = $post_data['customer_id'];

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
                    $reader->limitColumns(6);

//                  $reader->takeRows(1000);
                    $reader->limitRows(10001);

//                  $reader->ignoreEmpty();

//                  $data = $reader->all();
//                  $data = $reader->toArray();

                })->get()->toArray();

                $import_data = [];
                foreach($data as $key => $value)
                {
                    if(!empty($value['telephone']))
                    {
                        $temp_date['telephone'] = intval($value['telephone']);
                        $import_data[] = $temp_date;
                    }
                }

                // 启动数据库事务
                DB::beginTransaction();
                try
                {

                    foreach($import_data as $key => $value)
                    {
                        $item = new DK_Choice_Telephone_Bill;

                        $item->customer_id = $customer_id;
                        $item->creator_id = $me->id;
                        $item->created_type = 9;
                        $item->telephone = $value['telephone'];


                        $bool = $item->save();
                        if(!$bool) throw new Exception("DK_Choice_Clue--insert--fail");
                    }

                    DB::commit();
                    return response_success(['count'=>count($import_data)]);
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
                            $reader->limitRows(1000);

                        })->get()->toArray();

                        $order_data = [];
                        foreach($data as $key => $value)
                        {
                            if($value['customer_phone'])
                            {
                                $temp_date['customer_phone'] = intval($value['customer_phone']);
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
                                $order->customer_phone = $value['customer_phone'];

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
            $query =DK_Choice_User::select(['id','username as text'])
                ->where(['user_status'=>1]);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query =DK_Choice_User::select(['id','username as text'])->where('username','like',"%$keyword%")
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




    // 【线索】返回-列表-视图
    public function view_item_clue_list($post_data)
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




        // 工单ID
        if(!empty($post_data['clue_id']))
        {
            if(is_numeric($post_data['clue_id']) && $post_data['clue_id'] > 0) $view_data['clue_id'] = $post_data['clue_id'];
            else $view_data['clue_id'] = '';
        }
        else $view_data['clue_id'] = '';

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




        // 客户
        if(!empty($post_data['customer_id']))
        {
            if(is_numeric($post_data['customer_id']) && $post_data['customer_id'] > 0) $view_data['customer_id'] = $post_data['customer_id'];
            else $view_data['customer_id'] = -1;
        }
        else $view_data['customer_id'] = -1;




        // 项目
        if(!empty($post_data['project_id']))
        {
            if(is_numeric($post_data['project_id']) && $post_data['project_id'] > 0)
            {
                $project = DK_Choice_Project::select(['id','name'])->find($post_data['project_id']);
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
        if(!empty($post_data['customer_name']))
        {
            if($post_data['customer_name']) $view_data['customer_name'] = $post_data['customer_name'];
            else $view_data['customer_name'] = '';
        }
        else $view_data['customer_'] = '';
        // 客户电话
        if(!empty($post_data['customer_phone']))
        {
            if($post_data['customer_phone']) $view_data['customer_phone'] = $post_data['customer_phone'];
            else $view_data['customer_phone'] = '';
        }
        else $view_data['customer_phone'] = '';

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


        $customer_list = DK_Choice_Customer::select('id','username')->where('user_category',11)->get();
        $view_data['customer_list'] = $customer_list;

        $customer_preferential_list = DK_Choice_Customer::select('id','username')->where('user_category',11)->where('is_preferential',1)->get();
        $view_data['customer_preferential_list'] = $customer_preferential_list;

        $department_district_list = DK_Department::select('id','name')->where('department_type',11)->get();
        $view_data['department_district_list'] = $department_district_list;


        $project_list = DK_Choice_Project::select('id','name')->whereIn('item_type',[1,21])->get();
        $view_data['project_list'] = $project_list;

        $district_city_list = DK_Choice_District::select('id','district_city')->whereIn('district_status',[1])->get();
        $view_data['district_city_list'] = $district_city_list;

        if(!empty($post_data['district_city']))
        {
            $district_district_list = DK_Choice_District::select('district_district')->where('district_city',$post_data['district_city'])->whereIn('district_status',[1])->get();
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

        $view_data['menu_active_of_clue_list'] = 'active menu-open';

        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.clue-list';
        return view($view_blade)->with($view_data);
    }
    // 【线索】返回-列表-数据
    public function get_item_clue_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Choice_Clue::select('*')
//            ->selectAdd(DB::Raw("FROM_UNIXTIME(assign_time, '%Y-%m-%d') as assign_date"))
            ->with([
                'creator',
                'owner'=>function($query) { $query->select('id','username'); },
                'customer_er'=>function($query) { $query->select('id','username'); },
                'inspector',
                'deliverer',
                'project_er'
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
//            $subordinates = DK_Choice_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $subordinates_subordinates = DK_Choice_User::select('id')->whereIn('superior_id',$subordinates)->get()->pluck('id')->toArray();
//            $subordinates_list = array_merge($subordinates_subordinates,$subordinates);
//            $subordinates_list[] = $me->id;
//            $query->whereIn('creator_id',$subordinates_list);
            $district_staff_list = DK_Choice_User::select('id')->where('department_district_id',$me->department_district_id)->get()->pluck('id')->toArray();
            $query->whereIn('creator_id',$district_staff_list);
        }
        // 客服经理
        if($me->user_type == 81)
        {
//            $subordinates = DK_Choice_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $subordinates_subordinates = DK_Choice_User::select('id')->whereIn('superior_id',$subordinates)->get()->pluck('id')->toArray();
//            $subordinates_list = array_merge($subordinates_subordinates,$subordinates);
//            $subordinates_list[] = $me->id;
//            $query->whereIn('creator_id',$subordinates_list);
            $district_staff_list = DK_Choice_User::select('id')->where('department_district_id',$me->department_district_id)->get()->pluck('id')->toArray();
            $query->whereIn('creator_id',$district_staff_list);
        }
        // 客服主管
        if($me->user_type == 84)
        {
//            $subordinates = DK_Choice_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $subordinates[] = $me->id;
//            $query->whereIn('creator_id',$subordinates);
            $group_staff_list = DK_Choice_User::select('id')->where('department_group_id',$me->department_group_id)->get()->pluck('id')->toArray();
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
//            $subordinates = DK_Choice_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $query->where('is_published','<>',0)->whereHas('project_er', function ($query) use ($subordinates) {
//                $query->whereIn('user_id', $subordinates);
//            });
            // 多对对
            $subordinates = DK_Choice_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
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

        if(!empty($post_data['customer_name'])) $query->where('customer_name', $post_data['customer_name']);
        if(!empty($post_data['customer_phone'])) $query->where('customer_phone', 'like', "%{$post_data['customer_phone']}");

        if(!empty($post_data['assign'])) $query->whereDate(DB::Raw("from_unixtime(created_at)"), $post_data['assign']);
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
        if(isset($post_data['customer']))
        {
            if(!in_array($post_data['customer'],[-1]))
            {
                $query->where('customer_id', $post_data['customer']);
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

        // 是否+V
        if(!empty($post_data['is_wx']))
        {
            if(!in_array($post_data['is_wx'],[-1]))
            {
                $query->where('is_wx', $post_data['is_wx']);
            }
        }

        // 销售状态
        if(isset($post_data['sale_status']))
        {
            if(!in_array($post_data['sale_status'],["-1"]))
            {
                $query->where('sale_status', $post_data['sale_status']);
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
                    $customer_phone = $v->customer_phone;
                    $v->customer_phone = substr($customer_phone, 0, 3).'****'.substr($customer_phone, -4);
                }
            }
            else if(in_array($me->user_type,[41,81,84,88]))
            {
                $time = time();
                if(!$v->is_me || (($v->published_at > 0) && (($time - $v->published_at) > 86400)))
                {
//                    $len = strlen($customer_phone);  // 字符串长度
                    $customer_phone = $v->customer_phone;
                    if(is_numeric($customer_phone))
                    {
                        $v->customer_phone = substr($customer_phone, 0, 3).'****'.substr($customer_phone, -4);
                    }
                }
            }

        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    // 【线索】【修改记录】返回-列表-视图
    public function view_item_clue_modify_record($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $return['menu_active_of_clue_modify_list'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.clue-modify-list';
        return view($view_blade)->with($return);
    }
    // 【线索】【修改记录】返回-列表-数据
    public function get_item_clue_modify_record_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Choice_Record::select('*')
            ->with([
                'creator'=>function($query) { $query->select('id','username'); },
                'choice_staff_er'=>function($query) { $query->select('id','username'); },
                'customer_staff_er'=>function($query) { $query->select('id','username'); }
            ])
            ->where(['clue_id'=>$id]);
//            ->where(['record_object'=>21,'operate_object'=>61,'item_id'=>$id]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        if(!in_array($me->user_type,[0,1,9,11,61,66]))
        {
            $query->whereNotIn('operate_category',[96]);
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


    // 【线索】返回-导入-视图
    public function view_item_clue_import()
    {
        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9])) return view(env('TEMPLATE_ROOT_FRONT').'errors.404');

        $operate_category = 'item';
        $operate_type = 'item';
        $operate_type_text = '线索';
        $title_text = '导入'.$operate_type_text;
        $list_text = $operate_type_text.'列表';
        $list_link = '/item/clue-list';

        $return['operate'] = 'create';
        $return['operate_id'] = 0;
        $return['operate_category'] = $operate_category;
        $return['operate_type'] = $operate_type;
        $return['operate_type_text'] = $operate_type_text;
        $return['title_text'] = $title_text;
        $return['list_text'] = $list_text;
        $return['list_link'] = $list_link;

        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.clue-edit-for-import';
        return view($view_blade)->with($return);
    }
    // 【线索】保存-导入-数据
    public function operate_item_clue_import_save($post_data)
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
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

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
                    $reader->limitColumns(6);

//                  $reader->takeRows(1000);
                    $reader->limitRows(2001);

//                  $reader->ignoreEmpty();

//                  $data = $reader->all();
//                  $data = $reader->toArray();

                })->get()->toArray();

                $order_data = [];
                foreach($data as $key => $value)
                {
                    if(!empty($value['client_phone']))
                    {
                        $temp_date['client_name'] = $value['client_name'];
                        $temp_date['client_phone'] = intval($value['client_phone']);
                        $temp_date['location_city'] = $value['location_city'];
                        $temp_date['location_district'] = $value['location_district'];
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
                        $order = new DK_Choice_Clue;

                        $order->project_id = $project_id;
                        $order->creator_id = $me->id;
                        $order->created_type = 9;
                        $order->client_name = $value['client_name'];
                        $order->client_phone = $value['client_phone'];
                        $order->location_city = $value['location_city'];
                        $order->location_district = $value['location_district'];
                        $order->recording_address = $value['recording_address'];
                        $order->description = $value['description'];


                        $bool = $order->save();
                        if(!$bool) throw new Exception("DK_Choice_Clue--insert--fail");
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
                            $reader->limitRows(1000);

                        })->get()->toArray();

                        $order_data = [];
                        foreach($data as $key => $value)
                        {
                            if($value['customer_phone'])
                            {
                                $temp_date['customer_phone'] = intval($value['customer_phone']);
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
                                $order->customer_phone = $value['customer_phone'];

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


        $district_city_list = DK_Choice_District::select('id','district_city')->whereIn('district_status',[1])->get();
        $return['district_city_list'] = $district_city_list;

//        $district_district_list = DK_Choice_District::select('district_district')->where('district_city',$post_data['district_city'])->whereIn('district_status',[1])->get();
//        if(count($district_district_list) > 0)
//        {
//            $district_district_array = explode("-",$district_district_list[0]->district_district);
//            $return['district_district_list'] = $district_district_array;
//        }
//        else
//        {
//            $return['district_district_list'] = [];
//        }

        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.order-edit';
        return view($view_blade)->with($return);
    }
    // 【工单】返回-编辑-视图
    public function view_item_order_edit()
    {
        $this->get_me();
        $me = $this->me;

        $id = request("id",0);
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.order-edit';

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


        $district_city_list = DK_Choice_District::select('id','district_city')->whereIn('district_status',[1])->get();
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
//                if($mine->deleted_at) return view(env('TEMPLATE_DK_ADMIN_2').'entrance.errors.404');
//                else
                {
//                    $mine->custom = json_decode($mine->custom);
//                    $mine->custom2 = json_decode($mine->custom2);
//                    $mine->custom3 = json_decode($mine->custom3);

                    $district_district_list = DK_Choice_District::select('district_district')->where('district_city',$mine->location_city)->whereIn('district_status',[1])->get();
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
            else return view(env('TEMPLATE_DK_ADMIN_2').'entrance.errors.404');
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
            'customer_name.required' => '请填写客户信息！',
            'customer_phone.required' => '请填写客户电话！',
            'customer_phone.numeric' => '客户电话格式有误！',
            'customer_intention.required' => '请选择客户意向！',
//            'location_city.required' => '请选择城市！',
//            'location_district.required' => '请选择行政区！',
            'description.required' => '请输入通话小结！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'project_id' => 'required|numeric|min:1',
            'customer_name' => 'required',
            'customer_phone' => 'required|numeric',
            'customer_intention' => 'required',
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

//            $is_repeat = DK_Order::where('customer_phone',$post_data['customer_phone'])->where('project_id',$post_data['project_id'])->count("*");
        }
        else if($operate == 'edit') // 编辑
        {
            $mine = DK_Order::find($operate_id);
            if(!$mine) return response_error([],"该工单不存在，刷新页面重试！");

            if(in_array($me->user_type,[84,88]) && $mine->creator_id != $me->id) return response_error([],"该【工单】不是你的，你不能操作！");

//            $is_repeat = DK_Order::where('customer_phone',$post_data['customer_phone'])->where('project_id',$post_data['project_id'])->where('id','<>',$operate_id)->count("*");
        }
        else return response_error([],"参数有误！");

//        $post_data['is_repeat'] = $is_repeat;

        if(!empty($post_data['project_id']))
        {
            $project = DK_Choice_Project::find($post_data['project_id']);
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

        if($column_key == "customer_phone")
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
            if($column_key == "customer_phone")
            {
                $project_id = $item->project_id;
                $customer_phone = $item->customer_phone;

                $is_repeat = DK_Order::where(['project_id'=>$project_id,'customer_phone'=>$column_value])
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
                    $record = new DK_Choice_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["clue_id"] = $id;
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
                    $record = new DK_Choice_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["clue_id"] = $id;
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

        if(in_array($me->user_type,['customer_id','project_id']))
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
                    $project = DK_Choice_Project::withTrashed()->find($column_value);
                    if(!$project) throw new Exception("该【项目】不存在，刷新页面重试！");

                    $project_id = $item->project_id;
                    $customer_phone = $item->customer_phone;

                    $is_repeat = DK_Order::where(['project_id'=>$column_value,'customer_phone'=>$customer_phone])
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
                    $record = new DK_Choice_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["clue_id"] = $id;
                    $record_data["operate_object"] = 71;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = $after;

                    if(in_array($column_key,['customer_id','project_id']))
                    {
                        $record_data["before_id"] = $before;
                        $record_data["after_id"] = $column_value;
                    }



                    if($column_key == 'customer_id')
                    {
                        $record_data["before_customer_id"] = $before;
                        $record_data["after_customer_id"] = $column_value;
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
                                $record = new DK_Choice_Record;

                                $record_data["ip"] = Get_IP();
                                $record_data["record_object"] = 21;
                                $record_data["record_category"] = 11;
                                $record_data["record_type"] = 1;
                                $record_data["creator_id"] = $me->id;
                                $record_data["clue_id"] = $order_id;
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
                        $record = new DK_Choice_Record;

                        $record_data["ip"] = Get_IP();
                        $record_data["record_object"] = 21;
                        $record_data["record_category"] = 11;
                        $record_data["record_type"] = 1;
                        $record_data["creator_id"] = $me->id;
                        $record_data["clue_id"] = $order_id;
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
                $record = new DK_Choice_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["clue_id"] = $item->order_id;
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

        $item = DK_Order::with(['customer_er','car_er','trailer_er'])->withTrashed()->find($id);
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

        $item = DK_Order::with(['customer_er','car_er','trailer_er'])->withTrashed()->find($id);
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


        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.order-info-html';
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


        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.order-assign-html-for-attachment';
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
                    $record = new DK_Choice_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["clue_id"] = $item_id;
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
                    $record = new DK_Choice_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["clue_id"] = $item_id;
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
        $customer_phone = $item->customer_phone;

        $is_repeat = DK_Order::where(['project_id'=>$project_id,'customer_phone'=>$customer_phone])
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
                $record = new DK_Choice_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["clue_id"] = $id;
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
                $record = new DK_Choice_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["clue_id"] = $id;
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
                $record = new DK_Choice_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["clue_id"] = $id;
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
                $record = new DK_Choice_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["clue_id"] = $id;
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
                $record = new DK_Choice_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["clue_id"] = $id;
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
                $record = new DK_Choice_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["clue_id"] = $id;
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
    // 【工单】交付
    public function operate_item_order_deliver($post_data)
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

        $item = DK_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $customer_id = $post_data["customer_id"];
        if($customer_id == "-1")
        {
            $project = DK_Choice_Project::find($item->project_id);
            if($project->customer_id != 0) $customer_id = $project->customer_id;
        }
//        $customer = DK_Choice_Customer::find($customer_id);
//        if(!$customer) return response_error([],"客户不存在！");

        $delivered_result = $post_data["delivered_result"];
        if(!in_array($delivered_result,config('info.delivered_result'))) return response_error([],"交付结果参数有误！");

        $before = $item->delivered_result;

        $delivered_description = $post_data["delivered_description"];
        $recording_address = $post_data["recording_address"];

        $is_distributive_condition = $post_data["is_distributive_condition"];

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if($customer_id != "-1" && $delivered_result == "已交付")
            {
                $pivot_delivery = DK_Pivot_Client_Delivery::where(['pivot_type'=>95,'order_id'=>$item->id])->first();
                if($pivot_delivery)
                {
                    $pivot_delivery->customer_id = $customer_id;
                    $pivot_delivery->delivered_result = $delivered_result;
                    $bool_0 = $pivot_delivery->save();
                    if(!$bool_0) throw new Exception("pivot_customer_delivery--update--fail");
                }
                else
                {
                    $pivot_delivery = new DK_Pivot_Client_Delivery;
                    $pivot_delivery_data["pivot_type"] = 95;
                    $pivot_delivery_data["customer_id"] = $customer_id;
                    $pivot_delivery_data["order_id"] = $item->id;
                    $pivot_delivery_data["project_id"] = $item->project_id;
                    $pivot_delivery_data["customer_phone"] = $item->customer_phone;
                    $pivot_delivery_data["delivered_result"] = $delivered_result;
                    $pivot_delivery_data["creator_id"] = $me->id;

                    $bool_0 = $pivot_delivery->fill($pivot_delivery_data)->save();
                    if(!$bool_0) throw new Exception("insert--pivot_customer_delivery--fail");
                }
            }

            $item->is_distributive_condition = $is_distributive_condition;
            $item->customer_id = $customer_id;
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
                $record = new DK_Choice_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["clue_id"] = $id;
                $record_data["operate_object"] = 71;
                $record_data["operate_category"] = 95;
                $record_data["operate_type"] = 1;
                $record_data["column_name"] = "delivered_result";

                $record_data["before"] = $before;
                $record_data["after"] = $delivered_result;

//                $record_data["before_customer_id"] = $before;
//                $record_data["after_customer_id"] = $customer_id;

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
//            'customer_id.required' => 'customer_id.required.',
            'delivered_result.required' => 'delivered_result.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'ids' => 'required',
//            'customer_id' => 'required',
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

        $customer_id = $post_data["customer_id"];
//        $customer = DK_Choice_Customer::find($customer_id);
//        if(!$customer) return response_error([],"客户不存在！");

        $delivered_description = $post_data["delivered_description"];

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $delivered_para['customer_id'] = $customer_id;
            $delivered_para['deliverer_id'] = $me->id;
            $delivered_para['delivered_status'] = 1;
            $delivered_para['delivered_result'] = $delivered_result;
            $delivered_para['delivered_at'] = time();

//            $bool = DK_Order::whereIn('id',$ids_array)->update($delivered_para);
//            if(!$bool) throw new Exception("item--update--fail");
//            else
//            {
//            }

            foreach($ids_array as $key => $id)
            {
                $this_customer_id = $customer_id;

                $item = DK_Order::withTrashed()->find($id);
                if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

                if($this_customer_id == "-1")
                {
                    $project = DK_Choice_Project::find($item->project_id);
                    if($project->customer_id != 0) $this_customer_id = $project->customer_id;
                }


                if($this_customer_id != "-1" && $delivered_result == "已交付")
                {
                    $pivot_delivery = DK_Pivot_Client_Delivery::where(['pivot_type'=>95,'order_id'=>$id])->first();
                    if($pivot_delivery)
                    {
                        $pivot_delivery->customer_id = $this_customer_id;
                        $pivot_delivery->delivered_result = $delivered_result;
                        $bool_0 = $pivot_delivery->save();
                        if(!$bool_0) throw new Exception("pivot_customer_delivery--update--fail");
                    }
                    else
                    {
                        $pivot_delivery = new DK_Pivot_Client_Delivery;
                        $pivot_delivery_data["pivot_type"] = 95;
                        $pivot_delivery_data["customer_id"] = $this_customer_id;
                        $pivot_delivery_data["order_id"] = $item->id;
                        $pivot_delivery_data["project_id"] = $item->project_id;
                        $pivot_delivery_data["customer_phone"] = $item->customer_phone;
                        $pivot_delivery_data["delivered_result"] = $delivered_result;
                        $pivot_delivery_data["creator_id"] = $me->id;

                        $bool_0 = $pivot_delivery->fill($pivot_delivery_data)->save();
                        if(!$bool_0) throw new Exception("insert--pivot_customer_delivery--fail");
                    }
                }


                $before = $item->delivered_result;

                $item->customer_id = $this_customer_id;
                $item->deliverer_id = $me->id;
                $item->delivered_status = 1;
                $item->delivered_result = $delivered_result;
                $item->delivered_description = $delivered_description;
                $item->delivered_at = time();
                $bool = $item->save();
                if(!$bool) throw new Exception("item--update--fail");
                else
                {
                    $record = new DK_Choice_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["clue_id"] = $id;
                    $record_data["operate_object"] = 71;
                    $record_data["operate_category"] = 95;
                    $record_data["operate_type"] = 1;
                    $record_data["column_name"] = "delivered_result";

                    $record_data["before"] = $before;
                    $record_data["after"] = $delivered_result;

//                $record_data["before_customer_id"] = $before;
//                $record_data["after_customer_id"] = $customer_id;

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

        $customer_phone = $item->customer_phone;


        $order_repeat = DK_Order::select('id','customer_id','project_id','customer_phone','creator_id')
            ->with([
                'creator'=>function($query) { $query->select('id','username'); },
                'customer_er'=>function($query) { $query->select('id','username'); },
                'project_er'=>function($query) { $query->select('id','name'); }
            ])->where(['customer_phone'=>$customer_phone])
//            ->where('id','<>',$id)
//            ->where('delivered_status',1)
            ->where(function ($query) use($id) {
                $query->where('id','<>',$id)
                    ->orWhere(function($query) use($id) { $query->where('id',$id)->where('delivered_status',1); } );
            })
            ->where('is_published','>',0)
            ->get();
        $return['order_repeat'] = $order_repeat;

        $deliver_repeat = DK_Pivot_Client_Delivery::select('id','customer_id','order_id','project_id','customer_phone','creator_id')
            ->with([
                'creator'=>function($query) { $query->select('id','username'); },
                'customer_er'=>function($query) { $query->select('id','username'); },
                'project_er'=>function($query) { $query->select('id','name'); }
            ])->where(['customer_phone'=>$customer_phone])->get();
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
            'customer_id.required' => '请选择客户',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
            'customer_id' => 'required',
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
        $customer_phone = $item->customer_phone;

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $customer_id = $post_data["customer_id"];
        if($customer_id == "-1")
        {
            return response_error([],"请选择客户！");
//            $project = DK_Choice_Project::find($item->project_id);
//            if($project->customer_id != 0) $customer_id = $project->customer_id;
        }
        $customer = DK_Choice_Customer::find($customer_id);
        if(!$customer) return response_error([],"客户不存在！");

        $delivered_result = $post_data["delivered_result"];
        if(!in_array($delivered_result,config('info.delivered_result'))) return response_error([],"交付结果参数有误！");

        // 是否已经分发
        $is_distributed_list = DK_Pivot_Client_Delivery::where(['customer_id'=>$customer_id,'customer_phone'=>$customer_phone])->get();
        if(count($is_distributed_list) > 0)
        {
            return response_error([],"该客户已经交付过该号码，不可以重复分发！");
        }

        $is_order_list = DK_Order::with('project_er')->where(['customer_phone'=>$customer_phone,'delivered_result'=>'已交付'])->get();
        if(count($is_order_list) > 0)
        {
            foreach($is_order_list as $o)
            {
                if($o->customer_id == $customer_id)
                {
                    return response_error([],"该号码已在其他工单交付过该客户，不可以重复分发！");
                }

                if($o->project_er->customer_id == $customer_id)
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
//            if($customer_id != "-1")
//            {
            $pivot_delivery = new DK_Pivot_Client_Delivery;
            $pivot_delivery_data["pivot_type"] = 96;
            $pivot_delivery_data["customer_id"] = $customer_id;
            $pivot_delivery_data["order_id"] = $item->id;
            $pivot_delivery_data["project_id"] = $item->project_id;
            $pivot_delivery_data["customer_phone"] = $item->customer_phone;
            $pivot_delivery_data["delivered_result"] = $delivered_result;
            $pivot_delivery_data["creator_id"] = $me->id;

            $bool_0 = $pivot_delivery->fill($pivot_delivery_data)->save();
            if(!$bool_0) throw new Exception("insert--pivot_customer_delivery--fail");
//            }

//            $item->customer_id = $customer_id;
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
                $record = new DK_Choice_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["clue_id"] = $id;
                $record_data["operate_object"] = 71;
                $record_data["operate_category"] = 96;
                $record_data["operate_type"] = 1;
                $record_data["column_name"] = "customer_id";

                $record_data["before"] = $before;
                $record_data["after"] = $customer_id;

//                $record_data["before_customer_id"] = $before;
//                $record_data["after_customer_id"] = $customer_id;

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




    // 【工单】上架
    public function operate_item_clue_put_on_shelf($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
//            'customer_id.required' => 'customer_id.required.',
            'sale_type.required' => 'sale_type.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
//            'customer_id' => 'required',
            'sale_type' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'clue-put-on') return response_error([],"参数[operate]有误！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $item_id = $post_data['item_id'];
        $item = DK_Choice_Clue::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $sale_type = $post_data["sale_type"];
//        if(!in_array($assign_type,config('info.assign_type'))) return response_error([],"交付结果参数有误！");

        $customer_id = $post_data["customer_id"];

        if($sale_type == 66)
        {
            $customer = DK_Choice_Customer::find($customer_id);
            if(!$customer) return response_error([],"指定专享类型，必须选择客户！");
        }
        else $customer_id = 0;


//        $delivered_description = $post_data["delivered_description"];

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $delivered_para['assign_customer_id'] = $customer_id;
            $delivered_para['creator_id'] = $me->id;
//            $delivered_para['delivered_status'] = 1;
            $delivered_para['sale_type'] = $sale_type;
//            $delivered_para['delivered_at'] = time();


                $this_customer_id = $customer_id;


                $pivot_choice = DK_Choice_Pivot_Customer_Choice::where(['pivot_type'=>91,'clue_id'=>$item_id])->first();
                if($pivot_choice)
                {
                    $pivot_choice->assign_customer_id = $this_customer_id;
                    $pivot_choice->sale_type = $sale_type;
                    $bool_0 = $pivot_choice->save();
                    if(!$bool_0) throw new Exception("pivot_customer_choice--update--fail");
                }
                else
                {
                    $pivot_choice = new DK_Choice_Pivot_Customer_Choice;
                    $pivot_choice_data["pivot_type"] = 91;
                    $pivot_choice_data["sale_type"] = $sale_type;
                    $pivot_choice_data["customer_id"] = $this_customer_id;
                    $pivot_choice_data["clue_id"] = $item->id;
                    $pivot_choice_data["project_id"] = $item->project_id;
                    $pivot_choice_data["client_name"] = $item->client_name;
                    $pivot_choice_data["client_phone"] = $item->client_phone;
                    $pivot_choice_data["location_city"] = $item->location_city;
                    $pivot_choice_data["location_district"] = $item->location_district;
                    $pivot_choice_data["creator_id"] = $me->id;
                    $pivot_choice_data["sale_result"] = 1;
                    $pivot_choice_data["taken_at"] = time();

                    $bool_0 = $pivot_choice->fill($pivot_choice_data)->save();
                    if(!$bool_0) throw new Exception("insert--pivot_customer_choice--fail");

                    if($sale_type == 66)
                    {
                        $customer_u = DK_Choice_Customer::withTrashed()->lockForUpdate()->find($customer_id);
                        $customer_u->funds_obligation_total += $customer_u->cooperative_unit_price_3;
                        $bool_customer = $customer_u->save();
                        if(!$bool_customer) throw new Exception("DK_Choice_Customer--update--fail");
                    }
                }


                $before = $item->sale_type;

                $item->customer_id = $this_customer_id;
                $item->issuer_id = $me->id;
                $item->sale_type = $sale_type;
                $item->sale_status = 1;
                $item->sale_result = 1;
                $item->choice_id = $pivot_choice->id;
//                $item->delivered_result = $delivered_result;
//                $item->delivered_description = $delivered_description;
                $item->delivered_at = time();
                $bool = $item->save();
                if(!$bool) throw new Exception("item--update--fail");
                else
                {
                    $record = new DK_Choice_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 19;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["choice_staff_id"] = $me->id;
                    $record_data["clue_id"] = $item->id;
                    $record_data["choice_id"] = $pivot_choice->id;
                    $record_data["operate_object"] = 11;
                    $record_data["operate_category"] = 81;
                    $record_data["operate_type"] = 1;
                    $record_data["column_name"] = "put_on";

//                    $record_data["before"] = $before;
//                    $record_data["after"] = $sale_type;


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
    // 【工单】批量-上架
    public function operate_item_clue_put_on_shelf_by_bulk($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'ids.required' => 'ids.required.',
//            'customer_id.required' => 'customer_id.required.',
            'sale_type.required' => 'assign_type.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'ids' => 'required',
//            'customer_id' => 'required',
            'sale_type' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'clue-put-on-bulk') return response_error([],"参数[operate]有误！");
        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $sale_type = $post_data["sale_type"];
//        if(!in_array($assign_type,config('info.assign_type'))) return response_error([],"交付结果参数有误！");

        $customer_id = $post_data["customer_id"];

        if($sale_type == 66)
        {
            $customer = DK_Choice_Customer::find($customer_id);
            if(!$customer) return response_error([],"指定专享类型，必须选择客户！");
        }
        else $customer_id = 0;


//        $delivered_description = $post_data["delivered_description"];

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $delivered_para['assign_customer_id'] = $customer_id;
            $delivered_para['creator_id'] = $me->id;
//            $delivered_para['delivered_status'] = 1;
            $delivered_para['sale_type'] = $sale_type;
//            $delivered_para['delivered_at'] = time();

//            $bool = DK_Order::whereIn('id',$ids_array)->update($delivered_para);
//            if(!$bool) throw new Exception("item--update--fail");
//            else
//            {
//            }

            foreach($ids_array as $key => $id)
            {
                $this_customer_id = $customer_id;

                $item = DK_Choice_Clue::withTrashed()->find($id);
//                if(!$item) return response_error([],"该内容不存在，刷新页面重试！");
                if(!$item) throw new Exception("该内容不存在，刷新页面重试！");


                $pivot_choice = DK_Choice_Pivot_Customer_Choice::where(['pivot_type'=>91,'clue_id'=>$id])->first();
                if($pivot_choice)
                {
                    $pivot_choice->assign_customer_id = $this_customer_id;
                    $pivot_choice->sale_type = $sale_type;
                    $bool_0 = $pivot_choice->save();
                    if(!$bool_0) throw new Exception("pivot_customer_choice--update--fail");
                }
                else
                {
                    $pivot_choice = new DK_Choice_Pivot_Customer_Choice;
                    $pivot_choice_data["pivot_type"] = 91;
                    $pivot_choice_data["sale_type"] = $sale_type;
                    $pivot_choice_data["customer_id"] = $this_customer_id;
                    $pivot_choice_data["clue_id"] = $item->id;
                    $pivot_choice_data["project_id"] = $item->project_id;
                    $pivot_choice_data["client_name"] = $item->client_name;
                    $pivot_choice_data["client_phone"] = $item->client_phone;
                    $pivot_choice_data["location_city"] = $item->location_city;
                    $pivot_choice_data["location_district"] = $item->location_district;
                    $pivot_choice_data["creator_id"] = $me->id;
                    $pivot_choice_data["sale_result"] = 1;
                    $pivot_choice_data["taken_at"] = time();

                    $bool_0 = $pivot_choice->fill($pivot_choice_data)->save();
                    if(!$bool_0) throw new Exception("pivot_customer_choice--insert--fail");

                    if($sale_type == 66)
                    {
                        $customer_u = DK_Choice_Customer::withTrashed()->lockForUpdate()->find($customer_id);
                        $customer_u->funds_obligation_total += $customer_u->cooperative_unit_price_3;
                        $bool_customer = $customer_u->save();
                        if(!$bool_customer) throw new Exception("DK_Choice_Customer--update--fail");
                    }
                }


                $before = $item->sale_type;

                $item->customer_id = $this_customer_id;
                $item->issuer_id = $me->id;
                $item->sale_type = $sale_type;
                $item->sale_status = 1;
                $item->sale_result = 1;
                $item->choice_id = $pivot_choice->id;
//                $item->delivered_result = $delivered_result;
//                $item->delivered_description = $delivered_description;
                $item->delivered_at = time();
                $bool = $item->save();
                if(!$bool) throw new Exception("item--update--fail");
                else
                {
                    $record = new DK_Choice_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 19;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["choice_staff_id"] = $me->id;
                    $record_data["clue_id"] = $id;
                    $record_data["choice_id"] = $pivot_choice->id;
                    $record_data["operate_object"] = 11;
                    $record_data["operate_category"] = 81;
                    $record_data["operate_type"] = 1;
                    $record_data["column_name"] = "put_on";

//                    $record_data["before"] = $before;
//                    $record_data["after"] = $sale_type;


                    $bool_1 = $record->fill($record_data)->save();
                    if(!$bool_1) throw new Exception("record--insert--fail");
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
    // 【工单】下架
    public function operate_item_clue_put_off_shelf($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
//            'customer_id.required' => 'customer_id.required.',
//            'sale_type.required' => 'sale_type.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
//            'customer_id' => 'required',
//            'sale_type' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'clue-put-off') return response_error([],"参数[operate]有误！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $item_id = $post_data['item_id'];
        $item = DK_Choice_Clue::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");
        if(!in_array($item->sale_result,[0,4])) return response_error([],"该【上架线索】不能被删除！");

//        $sale_type = $post_data["sale_type"];
//        if(!in_array($assign_type,config('info.assign_type'))) return response_error([],"交付结果参数有误！");


        $pivot_choice = DK_Choice_Pivot_Customer_Choice::where(['pivot_type'=>91,'clue_id'=>$item->choice_id])->first();
        if($pivot_choice)
        {
            dd($pivot_choice);
            $pivot_choice_id = $pivot_choice->id;
            if(!in_array($pivot_choice->sale_result,[0,4])) return response_error([],"该【上架线索】不能被删除！");
        }
        else $pivot_choice_id = 0;

//        dd(8);

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
//            $delivered_para['assign_customer_id'] = 0;
//            $delivered_para['creator_id'] = $me->id;
//            $delivered_para['delivered_status'] = 1;
//            $delivered_para['sale_type'] = $sale_type;
//            $delivered_para['delivered_at'] = time();


            if($pivot_choice)
            {
                $bool_0 = $pivot_choice->delete();  // 普通删除
                if(!$bool_0) throw new Exception("pivot_customer_choice--delete--fail");
            }


//            $before = $item->sale_type;

//            $item->customer_id = 0;
//            $item->issuer_id = $me->id;
            $item->sale_status = 9;
//            $item->choice_id = 0;
//                $item->delivered_result = $delivered_result;
//                $item->delivered_description = $delivered_description;
//            $item->delivered_at = time();
            $bool = $item->save();
            if(!$bool) throw new Exception("DK_Choice_Clue--update--fail");
            else
            {
                $record = new DK_Choice_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 19;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["choice_staff_id"] = $me->id;
                $record_data["clue_id"] = $item->id;
                $record_data["choice_id"] = $pivot_choice_id;
                $record_data["operate_object"] = 11;
                $record_data["operate_category"] = 82;
                $record_data["operate_type"] = 1;
                $record_data["column_name"] = "put_off";

//                    $record_data["before"] = $before;
//                    $record_data["after"] = $sale_type;


                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("DK_Choice_Record--insert--fail");
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
     * 货架管理
     */
    // 【货架】返回-列表-视图
    public function view_item_choice_list($post_data)
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
        if(!empty($post_data['choice_id']))
        {
            if(is_numeric($post_data['choice_id']) && $post_data['choice_id'] > 0) $view_data['choice_id'] = $post_data['choice_id'];
            else $view_data['choice_id'] = '';
        }
        else $view_data['choice_id'] = '';
        // 工单ID
        if(!empty($post_data['clue_id']))
        {
            if(is_numeric($post_data['clue_id']) && $post_data['clue_id'] > 0) $view_data['clue_id'] = $post_data['clue_id'];
            else $view_data['clue_id'] = '';
        }
        else $view_data['clue_id'] = '';

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
        if(!empty($post_data['customer_id']))
        {
            if(is_numeric($post_data['customer_id']) && $post_data['customer_id'] > 0)
            {
                $customer = DK_Choice_Customer::select(['id','username'])->find($post_data['customer_id']);
                if($customer)
                {
                    $view_data['customer_id'] = $post_data['customer_id'];
                    $view_data['customer_name'] = $customer->username;
                }
                else $view_data['customer_id'] = -1;
            }
            else $view_data['customer_id'] = -1;
        }
        else $view_data['customer_id'] = -1;

        // 项目
        if(!empty($post_data['project_id']))
        {
            if(is_numeric($post_data['project_id']) && $post_data['project_id'] > 0)
            {
                $project = DK_Choice_Project::select(['id','name'])->find($post_data['project_id']);
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
        if(!empty($post_data['customer_name']))
        {
            if($post_data['customer_name']) $view_data['customer_name'] = $post_data['customer_name'];
            else $view_data['customer_name'] = '';
        }
        else $view_data['customer_'] = '';
        // 客户电话
        if(!empty($post_data['customer_phone']))
        {
            if($post_data['customer_phone']) $view_data['customer_phone'] = $post_data['customer_phone'];
            else $view_data['customer_phone'] = '';
        }
        else $view_data['customer_phone'] = '';

        // 审核状态
        if(!empty($post_data['inspected_status']))
        {
            $view_data['inspected_status'] = $post_data['inspected_status'];
        }
        else $view_data['inspected_status'] = -1;


        $customer_list = DK_Choice_Customer::select('id','username')->get();
        $view_data['customer_list'] = $customer_list;

        $project_list = DK_Choice_Project::select('id','name')->get();
        $view_data['project_list'] = $project_list;

        $view_data['menu_active_of_choice_list'] = 'active menu-open';

        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.choice-list';
        return view($view_blade)->with($view_data);
    }
    // 【货架】返回-列表-数据
    public function get_item_choice_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Choice_Pivot_Customer_Choice::select('*')
            ->withTrashed()
//            ->selectAdd(DB::Raw("FROM_UNIXTIME(assign_time, '%Y-%m-%d') as assign_date"))
//            ->where('customer_id',$me->id)
            ->with([
                'customer_er',
                'project_er',
                'clue_er',
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
        if(!empty($post_data['clue_id'])) $query->where('clue_id', $post_data['clue_id']);
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");
        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        if(!empty($post_data['customer_name'])) $query->where('customer_name', $post_data['customer_name']);
        if(!empty($post_data['customer_phone'])) $query->where('customer_phone', $post_data['customer_phone']);

        if(!empty($post_data['assign'])) $query->whereDate(DB::Raw("from_unixtime(created_at)"), $post_data['assign']);
        if(!empty($post_data['assign_start'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '>=', $post_data['assign_start']);
        if(!empty($post_data['assign_ended'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '<=', $post_data['assign_ended']);


        // 客户
        if(isset($post_data['customer']))
        {
            if(!in_array($post_data['customer'],[-1]))
            {
                $query->where('customer_id', $post_data['customer']);
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


    // 【线索】【修改记录】返回-列表-视图
    public function view_item_choice_modify_record($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $return['menu_active_of_choice_modify_list'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.choice-modify-list';
        return view($view_blade)->with($return);
    }
    // 【线索】【修改记录】返回-列表-数据
    public function get_item_choice_modify_record_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Choice_Record::select('*')
            ->with([
                'creator'=>function($query) { $query->select('id','username'); },
                'choice_staff_er'=>function($query) { $query->select('id','username'); },
                'customer_staff_er'=>function($query) { $query->select('id','username'); }
            ])
            ->where(['choice_id'=>$id]);
//            ->where(['record_object'=>21,'operate_object'=>61,'item_id'=>$id]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        if(!in_array($me->user_type,[0,1,9,11,61,66]))
        {
            $query->whereNotIn('operate_category',[96]);
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


    // 【货架】删除
    public function operate_item_choice_delete($post_data)
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
        if($operate != 'choice-delete') return response_error([],"参数[operate]有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数[ID]有误！");

        $item = DK_Choice_Pivot_Customer_Choice::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该【上架线索】不存在，刷新页面重试！");

        if(!in_array($item->sale_result,[0,4])) return response_error([],"该【上架线索】不能被删除！");

        $this->get_me();
        $me = $this->me;

        // 判断操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,61,66])) return response_error([],"用户类型错误！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[66]))
//        {
//            if($item->creator_id != $me->id) return response_error([],"你没有操作权限！");
//        }

        $clue = DK_Choice_Clue::find($item->clue_id);
        if(!$clue) return response_error([],"该【线索】不存在，刷新页面重试！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item_copy = $item;

            $item->timestamps = false;

            $bool = $item->delete();  // 普通删除
//            $bool = $item->forceDelete();  // 永久删除
            if(!$bool) throw new Exception("item--delete--fail");
            else
            {
                $clue->sale_status = 9;
                $clue->choice_id = 0;
                $clue->customer_id = 0;
                $bool_2 = $clue->save();
                if(!$bool_2) throw new Exception("clue--update--fail");

                $record = new DK_Choice_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 19;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["clue_id"] = $item_id;
                $record_data["choice_id"] = $item->clue_id;
                $record_data["operate_object"] = 11;
                $record_data["operate_category"] = 82;
                $record_data["operate_type"] = 1;

                $bool_3 = $record->fill($record_data)->save();
                if(!$bool_3) throw new Exception("insert--record--fail");
            }

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
    // 【货架】导出状态
    public function operate_item_choice_exported($post_data)
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
                $record = new DK_Choice_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["clue_id"] = $id;
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
    // 【货架】批量-导出状态
    public function operate_item_choice_bulk_exported($post_data)
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
                    $record = new DK_Choice_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["clue_id"] = $id;
                    $record_data["operate_object"] = 91;
                    $record_data["operate_category"] = 99;
                    $record_data["operate_type"] = 1;
                    $record_data["column_name"] = "is_exported";

                    $record_data["before"] = $before;
                    $record_data["after"] = $operate_result;

//                $record_data["before_customer_id"] = $before;
//                $record_data["after_customer_id"] = $customer_id;

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
        if(!empty($post_data['customer_id']))
        {
            if(is_numeric($post_data['customer_id']) && $post_data['customer_id'] > 0)
            {
                $customer = DK_Choice_Customer::select(['id','username'])->find($post_data['customer_id']);
                if($customer)
                {
                    $view_data['customer_id'] = $post_data['customer_id'];
                    $view_data['customer_name'] = $customer->username;
                }
                else $view_data['customer_id'] = -1;
            }
            else $view_data['customer_id'] = -1;
        }
        else $view_data['customer_id'] = -1;

        // 项目
        if(!empty($post_data['project_id']))
        {
            if(is_numeric($post_data['project_id']) && $post_data['project_id'] > 0)
            {
                $project = DK_Choice_Project::select(['id','name'])->find($post_data['project_id']);
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
        if(!empty($post_data['customer_name']))
        {
            if($post_data['customer_name']) $view_data['customer_name'] = $post_data['customer_name'];
            else $view_data['customer_name'] = '';
        }
        else $view_data['customer_'] = '';
        // 客户电话
        if(!empty($post_data['customer_phone']))
        {
            if($post_data['customer_phone']) $view_data['customer_phone'] = $post_data['customer_phone'];
            else $view_data['customer_phone'] = '';
        }
        else $view_data['customer_phone'] = '';

        // 审核状态
        if(!empty($post_data['inspected_status']))
        {
            $view_data['inspected_status'] = $post_data['inspected_status'];
        }
        else $view_data['inspected_status'] = -1;


        $customer_list = DK_Choice_Customer::select('id','username')->get();
        $view_data['customer_list'] = $customer_list;

        $project_list = DK_Choice_Project::select('id','name')->get();
        $view_data['project_list'] = $project_list;

        $view_data['menu_active_of_distribution_list'] = 'active menu-open';

        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.item.distribution-list';
        return view($view_blade)->with($view_data);
    }
    // 【分发管理】返回-列表-数据
    public function get_item_distribution_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Pivot_Client_Delivery::select('*')
//            ->selectAdd(DB::Raw("FROM_UNIXTIME(assign_time, '%Y-%m-%d') as assign_date"))
//            ->where('customer_id',$me->id)
            ->with([
                'inspector_er',
                'customer_er',
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

        if(!empty($post_data['customer_name'])) $query->where('customer_name', $post_data['customer_name']);
        if(!empty($post_data['customer_phone'])) $query->where('customer_phone', $post_data['customer_phone']);

        if(!empty($post_data['assign'])) $query->whereDate(DB::Raw("from_unixtime(created_at)"), $post_data['assign']);
        if(!empty($post_data['assign_start'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '>=', $post_data['assign_start']);
        if(!empty($post_data['assign_ended'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '<=', $post_data['assign_ended']);


        // 客户
        if(isset($post_data['customer']))
        {
            if(!in_array($post_data['customer'],[-1]))
            {
                $query->where('customer_id', $post_data['customer']);
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


    // 【财务】【充值】返回-列表-视图
    public function view_finance_funds_recharge_list($post_data)
    {
        $this->get_me();
        $me = $this->me;

        // 客户
        if(!empty($post_data['customer_id']))
        {
            if(is_numeric($post_data['customer_id']) && $post_data['customer_id'] > 0)
            {
                $customer = DK_Choice_Customer::select(['id','username'])->find($post_data['customer_id']);
                if($customer)
                {
                    $view_data['customer_id'] = $post_data['customer_id'];
                    $view_data['customer_name'] = $customer->username;
                }
                else $view_data['customer_id'] = -1;
            }
            else $view_data['customer_id'] = -1;
        }
        else $view_data['customer_id'] = -1;

        $customer_list = DK_Choice_Customer::select('id','username')->get();
        $view_data['customer_list'] = $customer_list;

        $view_data['menu_active_of_finance_funds_recharge_list'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.finance.funds-recharge-list';
        return view($view_blade)->with($view_data);
    }
    // 【财务】【充值】返回-列表-数据
    public function get_finance_funds_recharge_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Choice_Funds_Recharge::select('*')
            ->with(['creator','customer_er']);

        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");


        if(!empty($post_data['type']))
        {
            if($post_data['type'] == "income")
            {
                $query->where('finance_type', 1);
            }
            else if($post_data['type'] == "refund")
            {
                $query->where('finance_type', 101);
            }
        }

        if(!empty($post_data['finance_type']))
        {
            if(in_array($post_data['finance_type'],[1,101]))
            {
                $query->where('finance_type', $post_data['finance_type']);
            }
        }

        if(!empty($post_data['transaction_time'])) $query->whereDate(DB::raw("FROM_UNIXTIME(transaction_time,'%Y-%m-%d')"), $post_data['transaction_time']);
        if(!empty($post_data['transaction_start'])) $query->whereDate(DB::raw("FROM_UNIXTIME(transaction_time,'%Y-%m-%d')"), '>=', $post_data['transaction_start']);
        if(!empty($post_data['transaction_ended'])) $query->whereDate(DB::raw("FROM_UNIXTIME(transaction_time,'%Y-%m-%d')"), '<=', $post_data['transaction_ended']);


        // 客户
        if(isset($post_data['customer']))
        {
            if(!in_array($post_data['customer'],[-1]))
            {
                $query->where('customer_id', $post_data['customer']);
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


    // 【财务】【结算】返回-列表-视图
    public function view_finance_funds_using_list($post_data)
    {
        $this->get_me();
        $me = $this->me;

        // 客户
        if(!empty($post_data['customer_id']))
        {
            if(is_numeric($post_data['customer_id']) && $post_data['customer_id'] > 0)
            {
                $customer = DK_Choice_Customer::select(['id','username'])->find($post_data['customer_id']);
                if($customer)
                {
                    $view_data['customer_id'] = $post_data['customer_id'];
                    $view_data['customer_name'] = $customer->username;
                }
                else $view_data['customer_id'] = -1;
            }
            else $view_data['customer_id'] = -1;
        }
        else $view_data['customer_id'] = -1;

        $customer_list = DK_Choice_Customer::select('id','username')->get();
        $view_data['customer_list'] = $customer_list;

        $view_data['menu_active_of_finance_funds_using_list'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.finance.funds-using-list';
        return view($view_blade)->with($view_data);
    }
    // 【财务】【结算】返回-列表-数据
    public function get_finance_funds_using_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Choice_Funds_Using::select('*')
            ->with(['creator','customer_er','clue_er','choice_er','telephone_er']);

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

        if(!empty($post_data['transaction_time'])) $query->whereDate(DB::raw("FROM_UNIXTIME(created_at,'%Y-%m-%d')"), $post_data['transaction_time']);
        if(!empty($post_data['transaction_start'])) $query->whereDate(DB::raw("FROM_UNIXTIME(created_at,'%Y-%m-%d')"), '>=', $post_data['transaction_start']);
        if(!empty($post_data['transaction_ended'])) $query->whereDate(DB::raw("FROM_UNIXTIME(created_at,'%Y-%m-%d')"), '<=', $post_data['transaction_ended']);


        // 客户
        if(isset($post_data['customer']))
        {
            if(!in_array($post_data['customer'],[-1]))
            {
                $query->where('customer_id', $post_data['customer']);
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
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    // 【财务】【日报】返回-列表-视图
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
            else $view_data['length'] = -1;
        }
        else $view_data['length'] = -1;
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



        // 客户
        if(!empty($post_data['customer_id']))
        {
            if(is_numeric($post_data['customer_id']) && $post_data['customer_id'] > 0)
            {
                $customer = DK_Choice_Customer::select(['id','username'])->find($post_data['customer_id']);
                if($customer)
                {
                    $view_data['customer_id'] = $post_data['customer_id'];
                    $view_data['customer_name'] = $customer->username;
                }
                else $view_data['customer_id'] = -1;
            }
            else $view_data['customer_id'] = -1;
        }
        else $view_data['customer_id'] = -1;

        $customer_list = DK_Choice_Customer::select('id','username')->get();
        $view_data['customer_list'] = $customer_list;


        $view_data['menu_active_of_finance_daily_list'] = 'active menu-open';

        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.finance.daily-list';
        return view($view_blade)->with($view_data);
    }
    // 【财务】返回-列表-数据
    public function get_finance_daily_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');


        // 团队统计
        $query_for_using = DK_Choice_Funds_Using::select('created_at')
            ->addSelect(DB::raw("
                    DATE(FROM_UNIXTIME(created_at)) as formatted_date,
                    count(*) as total_of_daily_count,
                    sum(transaction_amount) as total_of_daily_cost
                "))
            ->groupBy(DB::raw("DATE(FROM_UNIXTIME(created_at))"));
//            ->get()
//            ->keyBy(DB::raw("DATE(FROM_UNIXTIME(created_at))"))
//            ->toArray();

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
                    $query_for_using->whereYear(DB::raw("DATE(FROM_UNIXTIME(created_at))"), $month_year)
                        ->whereMonth(DB::raw("DATE(FROM_UNIXTIME(created_at))"), $month_month);
                }
            }
            else if($post_data['time_type'] == "date")
            {
                // 指定日期
                if(!empty($post_data['date']))
                {
                    $query_for_using->whereDate(DB::raw("DATE(FROM_UNIXTIME(created_at))"), $post_data['date']);
                }
            }
            else if($post_data['time_type'] == "period")
            {
                $query_for_using->where(function ($query1) use($post_data) {
                    $query1->whereDate(DB::raw("DATE(FROM_UNIXTIME(created_at))"), '>=', $post_data['assign_start'])
                        ->whereDate(DB::raw("DATE(FROM_UNIXTIME(created_at))"), '<=', $post_data['assign_ended']);

                });
            }
            else
            {}
        }


        // 客户
        if(isset($post_data['customer']))
        {
            if(!in_array($post_data['customer'],[-1]))
            {
                $query_for_using->where('customer_id', $post_data['customer']);
            }
        }

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
            $query_for_using->orderBy($field, $order_dir);
        }
        else $query_for_using->orderBy("formatted_date", "desc");

        if($limit == -1) $list = $query_for_using->get();
        else $list = $query_for_using->skip($skip)->take($limit)->get();
        $total = count($list);



        $total_data = [];
        $total_data['formatted_date'] = '统计';

        $total_data['total_of_daily_count'] = 0;
        $total_data['total_of_daily_cost'] = 0;

        foreach ($list as $k => $v)
        {

            $total_data['total_of_daily_count'] += $v->total_of_daily_count;
            $total_data['total_of_daily_cost'] += $v->total_of_daily_cost;
        }
        $list[] = $total_data;


        return datatable_response($list, $draw, $total);

    }


    // 【财务】【修改记录】返回-列表-视图
    public function view_finance_daily_modify_record($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $staff_list = DK_Choice_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();

        $return['staff_list'] = $staff_list;
        $return['menu_active_of_customer_modify_list'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.user.customer-modify-list';
        return view($view_blade)->with($return);
    }
    // 【财务】【修改记录】返回-列表-数据
    public function get_finance_daily_modify_record_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Choice_Record::select('*')
            ->with(['creator'])
            ->where(['record_object'=>21, 'operate_object'=>86,'item_id'=>$id]);

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

        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.finance.daily-list-build';
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


        $customer_list = DK_Choice_Customer::select('id','cooperative_unit_price')->get()->keyBy('id');
//        dd($customer_list->toArray());


        // 交付统计
        $delivery_statistic = DK_Pivot_Client_Delivery::select('id','customer_id','created_at')
            ->addSelect(DB::raw("
                    FROM_UNIXTIME(created_at,'%Y-%m-%d') as formatted_date,
                    FROM_UNIXTIME(created_at,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(created_at,'%e') as day,
                    count(*) as total_of_count
                "))
            ->whereDate(DB::Raw("from_unixtime(created_at)"), $post_data['assign_date'])
            ->groupBy(DB::raw("DATE(FROM_UNIXTIME(created_at))"))
            ->groupBy('customer_id')
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
                $mine_data['customer_id'] = $v->customer_id;
                $mine_data['assign_date'] = $assign_date;
                $mine_data['cooperative_unit_price'] = $customer_list[$v->customer_id]->cooperative_unit_price;
                $mine_data['delivery_quantity'] = $v->total_of_count;
                $mine_data['total_daily_cost'] = $mine_data['cooperative_unit_price'] * $v->total_of_count;

                $mine = DK_Customer_Finance_Daily::where(['customer_id'=>$v->customer_id,'assign_date'=>$assign_date])->first();
                if($mine)
                {
                }
                else
                {
                    $mine = new DK_Customer_Finance_Daily;
                    $bool = $mine->fill($mine_data)->save();
                    if($bool)
                    {
                    }
                    else throw new Exception("insert--DK_Customer_Finance_Daily--fail");
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

        $item = DK_Customer_Finance_Daily::withTrashed()->lockForUpdate()->find($id);
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
                    $record = new DK_Choice_Record;

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

        $item = DK_Customer_Finance_Daily::withTrashed()->find($id);
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
                    $record = new DK_Choice_Record;

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

        $item = DK_Customer_Finance_Daily::withTrashed()->find($id);
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
                    $record = new DK_Choice_Record;

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


        $staff_list = DK_Choice_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();
        $customer_list = DK_Choice_Customer::select('id','username')->where('user_category',11)->get();
        $project_list = DK_Choice_Project::select('id','name')->whereIn('item_type',[1,21])->get();
        $department_district_list = DK_Department::select('id','name')->where('department_type',11)->get();

        $view_data['staff_list'] = $staff_list;
        $view_data['customer_list'] = $customer_list;
        $view_data['project_list'] = $project_list;
        $view_data['department_district_list'] = $department_district_list;

        $view_data['menu_active_of_statistic_index'] = 'active menu-open';

        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.statistic.statistic-index';
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
        $user = DK_Choice_User::find($user_id);

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

        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.statistic.statistic-user';
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

        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.statistic.statistic-item';
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
        $customer_id = 0;
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
            if(!empty($post_data['customer']))
            {
                if(!in_array($post_data['customer'],[-1,0]))
                {
                    $customer_id = $post_data['customer'];
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
        if($customer_id) $query_for_order_this_month->where('customer_id',$customer_id);
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
        if($customer_id) $query_for_order_last_month->where('customer_id',$customer_id);
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
        $query_order_of_all = (clone $query)->where('created_type',1)
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
            ->where('created_type',1)
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
            ->where('created_type',1)
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
            ->where('created_type',1)
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
            ->where('created_type',1)
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
            ->where('created_type',1)
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
        $customer_isset = 0;
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
        if(isset($post_data['customer']))
        {
            if(!in_array($post_data['customer'],[-1]))
            {
                $customer_isset = 1;
                $customer_id = $post_data['customer'];
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
        if($customer_isset) $query_for_order_this_month->where('customer_id', $customer_id);


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
        if($customer_isset) $query_for_order_last_month->where('customer_id', $customer_id);



        $statistics_data_for_order_last_month = $query_for_order_last_month->get()->keyBy('day');
        $return_data['statistics_data_for_order_last_month'] = $statistics_data_for_order_last_month;


        return response_success($return_data,"");
    }


    // 【统计】排名
    public function view_statistic_rank()
    {
        $this->get_me();
        $me = $this->me;

        $department_district_list = DK_Department::select('id','name')->where('department_type',11)->get();
        $view_data['department_district_list'] = $department_district_list;

        if($me->user_type == 81)
        {
            $view_data['department_district_id'] = $me->department_district_id;
            $department_group_list = DK_Department::select('id','name')->where('superior_department_id',$me->department_district_id)->get();
            $view_data['department_group_list'] = $department_group_list;
        }

        $view_data['menu_active_of_statistic_rank'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.statistic.statistic-rank';
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





        $query = DK_Choice_User::select(['id','user_status','user_type','username','true_name','department_district_id','department_group_id'])
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
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.statistic.statistic-rank-by-staff';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_rank_by_staff($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Choice_User::select(['id','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
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
//            $subordinates_array = DK_Choice_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_Choice_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据部门查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 81)
        {
            // 根据属下查看
//            $subordinates_array = DK_Choice_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_Choice_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
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


        $query = DK_Choice_User::select(['id','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
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
//            $subordinates_array = DK_Choice_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_Choice_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据部门查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 81)
        {
            // 根据属下查看
//            $subordinates_array = DK_Choice_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_Choice_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
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
    // 【统计】部门排名
    public function view_statistic_rank_by_department()
    {
        $this->get_me();
        $me = $this->me;

        $view_data['menu_active_of_statistic_rank_by_department'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.statistic.statistic-rank-by-department';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_rank_by_department($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Choice_User::select(['id','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
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
//            $subordinates_array = DK_Choice_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_Choice_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
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

        $department_district_list = DK_Department::select('id','name')->where('department_type',11)->get();
        $view_data['department_district_list'] = $department_district_list;

        if($me->user_type == 81)
        {
            $view_data['department_district_id'] = $me->department_district_id;
            $department_group_list = DK_Department::select('id','name')->where('superior_department_id',$me->department_district_id)->get();
            $view_data['department_group_list'] = $department_group_list;
        }

        $view_data['menu_active_of_statistic_recent'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.statistic.statistic-recent';
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





        $query = DK_Choice_User::select(['id','user_status','user_type','username','true_name','department_district_id','department_group_id'])
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
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.statistic.statistic-customer-service';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_customer_service($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Choice_User::select(['id','user_status','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
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
//            $subordinates_array = DK_Choice_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_Choice_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
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



        $query = DK_Choice_User::select(['id','user_status','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
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
//            $subordinates_array = DK_Choice_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_Choice_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据部门查看
            $query->where('department_district_id', $me->department_district_id);
        }
        // 客服经理
        else if($me->user_type == 81)
        {
            // 根据属下查看
//            $subordinates_array = DK_Choice_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_Choice_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
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
        if(!in_array($me->user_type,[0,1,9,11,41,71,61])) return view($this->view_blade_403);

        $view_data['menu_active_of_statistic_inspector'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.statistic.statistic-inspector';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_inspector($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Choice_User::select(['id','user_status','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
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




        $query = DK_Choice_User::select(['id','mobile','user_status','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
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
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.statistic.statistic-deliverer';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_deliverer($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Choice_User::select(['id','user_status','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
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




        $query = DK_Choice_User::select(['id','mobile','user_status','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
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




    // 【统计】交付看板
    public function view_statistic_delivery()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,41,81,84,71,77,61,66])) return view($this->view_blade_403);

        $department_district_list = DK_Department::select('id','name')->where('department_type',11)->get();
        $view_data['department_district_list'] = $department_district_list;

        $view_data['menu_active_of_statistic_delivery'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.statistic.statistic-delivery';
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


        $query = DK_Choice_Project::select('*')
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
    public function view_statistic_delivery_by_customer()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61,66])) return view($this->view_blade_403);

        $department_district_list = DK_Department::select('id','name')->where('department_type',11)->get();
        $view_data['department_district_list'] = $department_district_list;

        $view_data['menu_active_of_statistic_delivery_by_customer'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.statistic.statistic-delivery-by-customer';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_delivery_by_customer($post_data)
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
        $query_order = DK_Order::select('customer_id')
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
            ->groupBy('customer_id')
            ->get()
            ->keyBy('customer_id')
            ->toArray();


        $query = DK_Choice_Customer::select('*')
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

        $department_district_list = DK_Department::select('id','name')->where('department_type',11)->get();
        $view_data['department_district_list'] = $department_district_list;

        $view_data['menu_active_of_statistic_delivery'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.statistic.statistic-delivery';
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


        $query = DK_Choice_Project::select('*')
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

        $department_district_list = DK_Department::select('id','name')->where('department_type',11)->get();
        $view_data['department_district_list'] = $department_district_list;

        $view_data['menu_active_of_statistic_project'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.statistic.statistic-project';
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


        $query = DK_Choice_Project::select('*')
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

        $department_district_list = DK_Department::select('id','name')->where('department_type',11)->get();
        $view_data['department_district_list'] = $department_district_list;

        $view_data['menu_active_of_statistic_department'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.statistic.statistic-department';
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

        $staff = DK_Choice_User::select(['id','user_status','user_type','username','true_name','department_district_id','department_group_id'])
            ->with([
                'department_district_er' => function($query) { $query->select(['id','name']); },
                'department_group_er' => function($query) { $query->select(['id','name']); }
            ])
            ->find($post_data['staff_id']);
        $view_data['staff'] = $staff;

        $view_data['title_text'] = $staff->username;
        $view_data['menu_active_of_statistic_department'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.statistic.statistic-staff-customer-service';
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
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

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
        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.statistic.statistic-list-for-all';
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

        $project_list = DK_Choice_Project::select('id','name')->whereIn('item_type',[1,21])->get();
        $staff_list = DK_Choice_User::select('id','username','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();
        $customer_list = DK_Choice_Customer::select('id','username','true_name')->where('user_category',11)->get();

        $view_data['project_list'] = $project_list;
        $view_data['staff_list'] = $staff_list;
        $view_data['customer_list'] = $customer_list;


        $view_data['menu_active_of_statistic_export'] = 'active menu-open';

        $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.statistic.statistic-export';
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
            $record_last = DK_Choice_Record::select('*')
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


        $customer_id = 0;
        $staff_id = 0;
        $project_id = 0;

        // 客户
        if(!empty($post_data['customer']))
        {
            if(!in_array($post_data['customer'],[-1,0]))
            {
                $customer_id = $post_data['customer'];
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
                $project_er = DK_Choice_Project::find($project_id);
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
                'customer_er'=>function($query) { $query->select('id','username','true_name'); },
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


        if($customer_id) $query->where('customer_id',$customer_id);
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

            $cellData[$k]['customer_er_name'] = $v['customer_er']['username'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            $cellData[$k]['creator_name'] = $v['creator']['true_name'];
            $cellData[$k]['team'] = $v['department_district_er']['name'].' - '.$v['department_group_er']['name'];
            $cellData[$k]['published_time'] = date('Y-m-d H:i:s', $v['published_at']);

            $cellData[$k]['project_er_name'] = $v['project_er']['name'];
//            $cellData[$k]['channel_source'] = $v['channel_source'];
            $cellData[$k]['customer_name'] = $v['customer_name'];
            $cellData[$k]['customer_phone'] = $v['customer_phone'];
            if(in_array($me->user_type,[71,77]))
            {
                $time = time();
                // if(($v['inspected_at'] > 0) && (($time - $v['inspected_at']) > 86400))
                if(($v['inspected_at'] > 0) && (!isToday($v['inspected_at'])))
                {
                    $customer_phone = $v['customer_phone'];
                    $cellData[$k]['customer_phone'] = substr($customer_phone, 0, 3).'****'.substr($customer_phone, -4);
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
            'customer_er_name'=>'客户',
            'delivered_at'=>'交付时间',
            'creator_name'=>'创建人',
            'team'=>'团队',
            'published_time'=>'提交时间',
            'project_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
            'customer_name'=>'客户姓名',
            'customer_phone'=>'客户电话',
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


        $record = new DK_Choice_Record;

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
                'customer_er'=>function($query) { $query->select('id','username','true_name'); },
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

            $cellData[$k]['customer_er_name'] = $v['customer_er']['username'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            $cellData[$k]['creator_name'] = $v['creator']['true_name'];
            $cellData[$k]['team'] = $v['department_district_er']['name'].' - '.$v['department_group_er']['name'];
            $cellData[$k]['published_time'] = date('Y-m-d H:i:s', $v['published_at']);

            $cellData[$k]['project_er_name'] = $v['project_er']['name'];
//            $cellData[$k]['channel_source'] = $v['channel_source'];
            $cellData[$k]['customer_name'] = $v['customer_name'];
            $cellData[$k]['customer_phone'] = $v['customer_phone'];
            if(in_array($me->user_type,[71,77]))
            {
                $time = time();
                // if(($v['inspected_at'] > 0) && (($time - $v['inspected_at']) > 86400))
                if(($v['inspected_at'] > 0) && (!isToday($v['inspected_at'])))
                {
                    $customer_phone = $v['customer_phone'];
                    $cellData[$k]['customer_phone'] = substr($customer_phone, 0, 3).'****'.substr($customer_phone, -4);
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
            'customer_er_name'=>'客户',
            'delivered_at'=>'交付时间',
            'creator_name'=>'创建人',
            'team'=>'团队',
            'published_time'=>'提交时间',
            'project_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
            'customer_name'=>'客户姓名',
            'customer_phone'=>'客户电话',
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


        $record = new DK_Choice_Record;

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





    // 【修改记录】返回-列表-数据
    public function get_record_list_for_all_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Choice_Record::select('*')->withTrashed()
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

            $subordinates_array = DK_Choice_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();

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


    // 【用户操作记录】
    public function record_for_user_operate($record_object,$record_category,$record_type,$creator_id,$item_id,$operate_object,$operate_category,$operate_type = 0,$column_key = '',$before = '',$after = '')
    {
        $record = new DK_Choice_Record;

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





    // 【API】接单
    public function operate_api_okcc_receivingResult($post_data)
    {
        header("Content-Type:application/json;charset=UTF-8");

//        $call = new DK_Choice_Call_Record;
//        $call_data['call_result_msg'] = 1;
//        $bool_c = $call->fill($call_data)->save();
//
//        $return['result']['error'] = 0;
//        $return['result']['msg'] = '';
//        return json_decode(json_encode($return));

        $messages = [
            'authentication.required' => 'authentication.required.',
            'notify.required' => 'notify.required.',
        ];
        $v = Validator::make($post_data, [
            'authentication' => 'required',
            'notify' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $time = time();

        $seq = $post_data['notify']['seq'];
        $userData = $post_data['notify']['userData'];
        $timeLength = $post_data['notify']['timeLength'];

        $customer_time_limit = 0;

//        if($userData == 'calling')
        if(true)
        {
            $call_data = $post_data['notify'];
            $call_data['apiType'] = $post_data['notify']['type'];
            $call_data['content'] = json_encode($post_data);

            $call = DK_Choice_Call_Record::find($userData);
            if($call)
            {
//                $telephone = DK_Choice_Telephone_Bill::find($call->telephone_id);
//                if($telephone)
//                {
//                }

                $customer = DK_Choice_Customer::find($call->customer_id);
                if($customer)
                {
                    $customer_time_limit = $customer->call_time_limit_for_telephone;
                }
                else $customer_time_limit = 0;
            }
            else
            {
                return response_error([],"该【通话】不存在！");
//                $call = new DK_Choice_Call_Record;
//
//                $call_data["ip"] = Get_IP();
//                $call_data["telephone"] = $post_data['notify']['callee'];
            }


            // 启动数据库事务
            DB::beginTransaction();
            try
            {
                $bool_c = $call->fill($call_data)->save();
                if(!$bool_c) throw new Exception("DK_Choice_Call_Record--fail--fail");

                if(($customer_time_limit > 0) && ($timeLength > 0) && ($timeLength >= $customer_time_limit))
                {
                    if($call->call_object == 0)
                    {

                    }
                    else if($call->call_object == 71)
                    {
                        $choice = DK_Choice_Pivot_Customer_Choice::find($call->choice_id);
                        if($choice->sale_result != 9)
                        {
                            // 自选记录
                            $choice->sale_result = 9;
                            $choice->purchaser_type = 1;
                            $choice->purchaser_id = $call->customer_staff_id;
                            $choice->purchased_at = $time;
                            $bool_c = $choice->save();
                            if(!$bool_c) throw new Exception("DK_Choice_Pivot_Customer_Choice--update--fail");


                            // 线索记录
                            if($choice->pivot_type == 91)
                            {
                                $clue = DK_Choice_Clue::find($choice->clue_id);
                                if(!$clue) throw new Exception("DK_Choice_Clue--not--find");

                                $clue->sale_result = 9;
                                $bool_clue = $clue->save();
                                if(!$bool_clue) throw new Exception("DK_Choice_Clue--update--fail");
                            }


                            // 客户总账
                            $customer_u = DK_Choice_Customer::withTrashed()->lockForUpdate()->find($call->customer_id);
                            if($choice->sale_type == 1)
                            {
                                $cooperative_unit_price = $customer_u->cooperative_unit_price_1;
                            }
                            else if($choice->sale_type == 11)
                            {
                                $cooperative_unit_price = $customer_u->cooperative_unit_price_2;
                            }
                            else if($choice->sale_type == 66)
                            {
                                $cooperative_unit_price = $customer_u->cooperative_unit_price_3;
                            }
                            else
                            {
                                $cooperative_unit_price = 0;
                            }
                            $customer_u->funds_obligation_total -= $cooperative_unit_price;
                            $customer_u->funds_consumption_total += $cooperative_unit_price;
                            $bool_customer = $customer_u->save();
                            if(!$bool_customer) throw new Exception("DK_Choice_Customer--update--fail");


                            // 消费记录
                            $using = new DK_Choice_Funds_Using;

                            $using_data["purchased_category"] = 99;
                            $using_data["purchased_type"] = 1;
                            $using_data["finance_object"] = 71;
                            $using_data["finance_category"] = 1;
                            $using_data["finance_type"] = 1;
                            $using_data["creator_id"] = 0;
                            $using_data["customer_staff_id"] = $call->customer_staff_id;
                            $using_data["customer_id"] = $call->customer_id;
                            $record_data["choice_id"] = $call->choice_id;
                            $using_data["clue_id"] = $call->clue_id;
                            $using_data["transaction_amount"] = $cooperative_unit_price;

                            $bool_u = $using->fill($using_data)->save();
                            if(!$bool_u) throw new Exception("DK_Choice_Funds_Using--insert--fail");
                        }
                    }
                    else if($call->call_object == 77)
                    {
                        $telephone = DK_Choice_Telephone_Bill::find($call->telephone_id);
                        if($telephone->sale_result != 9)
                        {
                            // 话单记录
                            $telephone->sale_result = 9;
                            $telephone->purchaser_type = 99;
                            $telephone->purchaser_id = $call->customer_staff_id;
                            $telephone->purchased_at = $time;
                            $bool_t = $telephone->save();
                            if(!$bool_t) throw new Exception("DK_Choice_Telephone_Bill--update--fail");


                            // 客户总账
                            $customer_u = DK_Choice_Customer::withTrashed()->lockForUpdate()->find($call->customer_id);
                            $cooperative_unit_price = $customer_u->cooperative_unit_price_of_telephone;  // 单价
                            $customer_u->funds_consumption_total += $cooperative_unit_price;  // 消费金额
                            $bool_customer = $customer_u->save();
                            if(!$bool_customer) throw new Exception("DK_Choice_Customer--update--fail");


                            // 消费记录
                            $using = new DK_Choice_Funds_Using;

                            $using_data["purchased_category"] = 99;
                            $using_data["purchased_type"] = 89;
                            $using_data["finance_object"] = 77;
                            $using_data["finance_category"] = 1;
                            $using_data["finance_type"] = 1;
                            $using_data["creator_id"] = 0;
                            $using_data["customer_staff_id"] = $call->customer_staff_id;
                            $using_data["customer_id"] = $call->customer_id;
                            $using_data["telephone_id"] = $call->telephone_id;
                            $using_data["transaction_amount"] = $cooperative_unit_price;

                            ;$bool_u = $using->fill($using_data)->save();
                            if(!$bool_u) throw new Exception("DK_Choice_Funds_Using--insert--fail");

                        }
                    }
                }


                // 操作记录
                $record = DK_Choice_Record::find($call->record_id);
                if($record)
                {
                    $record->after = $timeLength;
                    $bool_r = $record->save();
                }

                DB::commit();

                $return['result']['error'] = 0;
                $return['result']['msg'] = '';
                return json_decode(json_encode($return));
//                return response()->json($return);
//                return response_success(json_encode($return));

            }
            catch (Exception $e)
            {
                DB::rollback();
//            $msg = '操作失败，请重试！';
                $msg = $e->getMessage();
//            exit($e->getMessage());
//            return response_fail([],$msg);

                $return['result']['error'] = 1;
                $return['result']['msg'] = $msg;
                return json_decode(json_encode($return));
            }
        }



    }


}